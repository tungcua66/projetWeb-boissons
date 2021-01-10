<?php
  if (isset($_POST["filter_ing"])) {
    $database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    $query = $database_boissons->query("select rcp_name from recipes where rcp_id = (select rcp_id from indexes where ing_id = ("
    . "select ing_id from hierarchy where lower(ing_name) like \"" . strtolower($_POST["filter_ing"]) ."\"));");
    $array[] = [  ];
    while ($row = $query->fetchArray()) {
      array_push($array, $row["rcp_name"]);
    }
    echo json_encode($array);
    $database_boissons->close();
  }
?>
