<?php
  if (isset($_POST["filter_ing_super_categ"])) {
    $database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    $query = $database_boissons->query("select rcp_name from recipes where rcp_id in (select distinct(rcp_id) from indexes where ing_id in (with recursive cte_hierarchy (ing_id, ing_name) as (select ing_id, ing_name from hierarchy h where ing_super_categ like \"" . $_POST["filter_ing_super_categ"] . "\" or ing_name like \"" . $_POST["filter_ing_super_categ"] . "\" union all select h.ing_id, h.ing_name from hierarchy h join cte_hierarchy c on c.ing_name = h.ing_super_categ) select distinct(ing_id) from cte_hierarchy));");
    $array[] = [  ];
    while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
      array_push($array, $row["rcp_name"]);
    }
    echo json_encode($array);
    $database_boissons->close();
  }
?>
