<?php
  if (isset($_POST["ing_name"])) {
    $database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    $query = $database_boissons->query("select ing_super_categ from hierarchy where lower(ing_name) like \"" . strtolower($_POST["ing_name"]) . "\"");
    $array[] = [  ];
    while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
      array_push($array, $array, $row["ing_name"]);
    }
    echo json_encode($array);
    $database_boissons->close();
  }
?>
