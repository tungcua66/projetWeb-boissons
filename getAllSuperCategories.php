<?php
  if (isset($_POST["filter_ing"]) && trim($_POST["filter_ing"]) != "") {
    $database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    $query = $database_boissons->query("select ing_name from hierarchy where ing_sous_categ in (select distinct(ing_super_categ) from hierarchy where ing_super_categ like \"" . $_POST["filter_ing"] . "%\");");
    $array[] = [  ];
    while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
      array_push($array, $row["ing_name"]);
    }
    echo json_encode($array);
    $database_boissons->close();
  }
?>
