<?php
  if (isset($_POST["ing_super_categ"])) {
    $database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    $query = $database_boissons->query("select distinct(ing_name) from hierarchy where ing_super_categ like \"".$_POST["ing_super_categ"]."\";");
    $array[] = [  ];
    while ($row = $query->fetchArray()) {
      $is_null_query = $database_boissons->query("select distinct(ing_name) from hierarchy where ing_name like \""
                                . $row["ing_name"] . "\" and ing_sous_categ like \"null\";");
      if ($is_null_query->fetchArray()) {
        array_push($array, array($row["ing_name"], 1));
      } else {
        array_push($array, array($row["ing_name"], 0));
      }
    }
    echo json_encode($array);
    $database_boissons->close();
  }
?>
