<?php 
/*   display all php errors   */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SESSION) {
  $user_id = $_SESSION["user_id"];

  $rcp_name = $_POST["rcp_name"];
  if (isset($user_id) && isset($rcp_name)) {
    /*   open sqlite3 database   */
    $database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    if (isset($_POST["add_recipe"])) {
      $database_boissons->exec("insert into basket (user_id, rcp_id) values (" . $user_id . ", (select rcp_id from recipes where rcp_name like \"" . $rcp_name . "\"));");
    } else {
      $database_boissons->exec("delete from basket where user_id = \"" . $user_id . "\" and rcp_id = (select rcp_id from recipes where rcp_name like \"" . $rcp_name . "\");");
    }
    $database_boissons->close();
  }
} 
?>