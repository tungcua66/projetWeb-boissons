<?php
/*   display all php errors   */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*   install hierarchy, recipes and indexes databases if not existing already   */
include "install.php";

?>


<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


  <script type="text/javascript" src="index.js"></script>
</head>
<body>
  <?php
    /*   open sqlite3 database   */
    $database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
  ?>
  
  <h2>Boissons</h2>
  <div class="dropdown" style="position:relative">
    <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Boissons<span class="caret"></span></a>
    <ul class="dropdown-menu">
      <?php
        $query = $database_boissons->query("select distinct(ing_name) from hierarchy where lower(ing_super_categ) like 'aliment';");
        while ($row = $query->fetchArray()) {
          $categ = $row["ing_name"];
          echo "<li><a tabindex=\"-1\" value=\"".$categ."\" class=\"trigger right-caret\" href=\"#\">".$categ."<span class=\"caret\"></span></a><ul class=\"dropdown-menu sub-menu\"></ul></li>";
        }
      ?>
    </ul>
  </div>
  <input type="text" id="recipesSearchInput" onkeyup="filterRecipesOnType()" placeholder="Search for names.." title="Type in a name">

<ul id="recipesList">
  <?php
    $query = $database_boissons->query("select rcp_name, rcp_formula, rcp_prep from recipes;");
    while ($row = $query->fetchArray()) {
      $rcp_name = $row["rcp_name"];
      $rcp_formula = explode("|", $row["rcp_formula"]);
      $rcp_prep = $row["rcp_prep"];
      echo "<li class=\"recipeTitle\"><a>".$rcp_name."</a><ul class=\"recipeBlock\">";
      echo "<b>Ingredients:</b>";
      echo "<div class=\"list_indent\">";
      foreach ($rcp_formula as &$formula_instruction) {
        echo "<li>".$formula_instruction."</li>";
      }
      echo "</div><b>Préparation:</b><div class=\"list_indent\"><li>".$rcp_prep."</li></div></ul></li>";
    }
  ?>
</ul>


  <?php
    /*   close sqlite3 database connection   */
    $database_boissons->close();
  ?>
</body>
</html>
