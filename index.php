<?php
/*   display all php errors   */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*   install hierarchy, recipes and indexes databases if not existing already   */
include "install.php";
session_start();

function recipePhotoNameFromTitle($rcp_name) {
    $rcp_name_no_accents = strtr(utf8_decode($rcp_name), utf8_decode("àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ"), "aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY");
    return ucfirst(strtolower(str_replace(" ", "_", $rcp_name_no_accents)));
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Boissons</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="app.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="index.js"></script>
  <script src="https://kit.fontawesome.com/7c29e8f9e2.js" crossorigin="anonymous"></script>


</head>
<body>
  <?php
    /*   open sqlite3 database   */
    $database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
  ?>
  <div class="container">

    <h2>Boissons<i class="fas fa-glass-martini-alt"></i></h2>
    <?php

      if ($_SESSION && isset($_SESSION["username"]) && isset($_SESSION["user_id"])) {
          $username = $_SESSION["username"];
          $user_id = $_SESSION["user_id"];
          echo "<h3>Bonjour " . $username . "</h3>";
          echo "<a href=\"logout.php\" class=\"btn btn-primary\">Déconnexion</a>";
      } else if (!isset($_SESSION["username"])) {
          if (!isset($_SESSION["user_id"])) {
            $user_id = hexdec(uniqid());
            $_SESSION["user_id"] = $user_id;
          } else {
              $user_id = $_SESSION["user_id"];
          }
          echo "<a href=\"signup.php\" class=\"btn btn-primary\">Inscription</a>";
          echo "<a href=\"login.php\" class=\"btn btn-primary\">Connexion</a>";
      }
      //echo "<br>";
      if (!isset($_GET["basket"])) {
            echo "<a href=\"index.php?basket=true\" class=\"btn btn-primary\">Panier de recettes</a>";
      } else {
            echo "<a href=\"index.php\" class=\"btn btn-primary\">Toutes les recettes</a>";
      }
    ?>



    <div class="dropdown" style="position:relative">
      <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Ingrédients<span class="caret"></span></a>
      <br>
      <ul class="dropdown-menu">
        <?php
          $query = $database_boissons->query("select distinct(ing_name) from hierarchy where lower(ing_super_categ) like 'aliment';");
          while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $categ = $row["ing_name"];
            echo "<li><a tabindex=\"-1\" value=\"" . $categ . "\" class=\"trigger right-caret\" href=\"#\">" . $categ .
                 "<span class=\"caret\"></span></a><ul class=\"dropdown-menu sub-menu\"></ul></li>";
          }
        ?>
      </ul>
    </div>
    <a href="#" id="clearFilters" class="btn btn-primary">Effacer <i class="fas fa-trash-alt"></i></a>
    <input type="text" id="recipesSearchInput" onkeyup="filterRecipesOnType()" placeholder="Rechercher une recette" title="Recherche">

    <ul id="recipesList">
    <?php
      if (isset($_GET["basket"])) {
        $query = $database_boissons->query("select rcp_name, rcp_formula, rcp_prep from recipes where rcp_id in (select rcp_id from basket where user_id = \"" . $user_id . "\" order by rcp_id);");
      } else {
        $query = $database_boissons->query("select rcp_name, rcp_formula, rcp_prep from recipes;");
      }
      while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
        $rcp_name = $row["rcp_name"];
        $rcp_formula = explode("|", $row["rcp_formula"]);
        $rcp_prep = $row["rcp_prep"];
        echo "<li class=\"recipeTitle\"><a value=\"" . $rcp_name . "\">" . $rcp_name . "</a><ul class=\"recipeBlock\">";
        echo "<img src=\"Photos/" . recipePhotoNameFromTitle($rcp_name) . ".jpg\" alt=\"" . $rcp_name . "\" width=\"30%\">";
        echo "<br><b>Ingredients:</b>";
        echo "<div class=\"list_indent\">";
        foreach ($rcp_formula as &$formula_instruction) {
          echo "<li>".$formula_instruction."</li>";
        }
        echo "</div>";
        echo "<b>Préparation:</b>";
        echo "<div class=\"list_indent\"><li>" . $rcp_prep . "</li></div>";
        if (!isset($_GET["basket"])) {
            echo "<a class=\"addRecipeToBasket btn btn-primary\" value=\"" . $user_id . "\">Ajouter cette recette au panier</a>";
        } else {
            echo "<a class=\"removeRecipeFromBasket btn btn-primary\" value=\"" . $user_id . "\">Enlever cette recette du panier</a>";
        }
        echo "</ul></li>";
      }
    ?>
  </ul>
</div>
  <?php
    /*   close sqlite3 database connection   */
    $database_boissons->close();
  ?>
</body>
</html>
