<?php

function insert_ingredient_in_db($db, &$id, $c, $sous_c, $super_c) {
  if (is_string($c) && is_string($sous_c) && is_string($super_c)) {
      $sqlite_insert = "insert into hierarchy (ing_id, ing_name, ing_sous_categ, ing_super_categ) values (\""
                    . $id . "\", \""
                    . $c . "\", \""
                    . $sous_c . "\", \""
                    . $super_c . "\");";
      $db->exec($sqlite_insert);
      $id = $id + 1;
  }
}
/*   open database or create file it if not already existing   */
$database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
//$database_boissons->exec("drop table hierarchy;");
/*   verify if table exists, if not fill hierarchy table with data provided in Donnees.inc.php   */
$select_database_exists = $database_boissons->query("select name from sqlite_master where type=\"table\" and name=\"hierarchy\";");
$database_exists = $select_database_exists->fetchArray();
if (!$database_exists) {
    /*   load resource file   */
    include "Donnees.inc.php";

    $database_boissons->exec("create table if not exists hierarchy (ing_id integer primary key, ing_name text, ing_sous_categ text, ing_super_categ text);");
    $ingredient_id = 0;
    /*   loop over ingredients only   */
    foreach ($Hierarchie as $categ => $meta_categ) {
        $super_categ = array_key_exists("super-categorie", $meta_categ) ? $meta_categ["super-categorie"][0] : "null";
        if (array_key_exists("sous-categorie", $meta_categ)) {
            foreach ($meta_categ["sous-categorie"] as $sous_categ) {
                insert_ingredient_in_db($database_boissons, $ingredient_id, $categ, $sous_categ, $super_categ);
            }
        } else {
          if (is_string($categ) && is_string($sous_categ)) {
              insert_ingredient_in_db($database_boissons, $ingredient_id, $categ, "null", $super_categ);
          }
        }
    }
    $database_boissons->exec("create table if not exists recipes (rcp_id integer primary key, rcp_name text, rcp_formula text, rcp_prep text);");
    $database_boissons->exec("create table if not exists indexes (rcp_id integer, ing_id integer);");
    foreach ($Recettes as $recipe_id => $ingredients) {
      $title = $ingredients["titre"];
      $recipe = $ingredients["ingredients"];
      /*   replace quotes with backslashes to permit database insertion   */
      $prep = str_replace("\"", "\\", $ingredients["preparation"]);
      $index = $ingredients["index"];
      foreach ($index as $ing_nb => $ing_name) {
        $sql_query = "insert into indexes (rcp_id, ing_id) values (\""
                    . $recipe_id . "\", (select ing_id from hierarchy where ing_name like \"" . $ing_name . "\"));";
        $database_boissons->exec($sql_query);
      }
      $sqlite_insert = "insert into recipes (rcp_id, rcp_name, rcp_formula, rcp_prep) values (\""
                      . $recipe_id . "\", \""
                      . $title . "\", \""
                      . $recipe . "\", \""
                      . $prep . "\");";
      $database_boissons->exec($sqlite_insert);
    }
}
$database_boissons->close();
?>
