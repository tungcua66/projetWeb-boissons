<?php
  /*   display all php errors   */
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  
  session_start();
    
  if (isset($_GET["add"])){
    $database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    $username = $_POST["username"];
    $password = $_POST["password"];
    $password_repeat = $_POST["password_repeat"];
    if (isset($username) && isset($password) && isset($password_repeat)) {
      if ($password == $password_repeat && strlen($password) >= 8 && strlen($username) >= 3) {
        $sql_query = $database_boissons->query("select user_id from login where username like \"" . $username . "\";");
        if (!$sql_query->fetchArray(SQLITE3_ASSOC)) {
          $sql = "insert into login (user_id, username, password_hash) values (null, \""
                 . $username . "\", \"" . hash("sha512", trim($password)) . "\");";
          if(!$database_boissons->exec($sql)){
            echo $database_boissons->lastErrorMsg();
          } else {
            echo "Inscription réussie !\n";
            $sql_query = $database_boissons->query("select user_id from login where username like \"" . $username . "\";");
            if ($row = $sql_query->fetchArray(SQLITE3_ASSOC)) {
              $new_user_id = $row["user_id"];
              $guest_user_id = $_SESSION["user_id"];
              $sql_query = $database_boissons->query("select rcp_id from basket where user_id = \"" . $guest_user_id . "\";");
              while ($row = $sql_query->fetchArray(SQLITE3_ASSOC)) {
                $database_boissons->exec("insert into basket (user_id, rcp_id) values (" . $new_user_id . ", " . $row["rcp_id"] . ");");
              }
              $database_boissons->exec("delete from basket where user_id = \"" . $guest_user_id . "\";");
            }
            header("Location: index.php"); 
          }
        } else {
          echo "L'utilisateur " . $username . " existe déjà !\n";
        }
      } else {
          echo "Nom d'utilisateur: minimum 3 caractères<br>Mot de passe: minimum 8 caractères";
      }
    }
    $database_boissons->close();
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Inscription</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="app.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://kit.fontawesome.com/7c29e8f9e2.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
  <h2>Inscription <i class="fas fa-user-plus"></i></h2>
  <a href="index.php" class="btn btn-primary">Accueil <i class="fas fa-home"></i></a>
  <form role="form" method="post" action="signup.php?add=<?php echo uniqid()?>">
    <div class="form-group">
      <label for="username">Utilisateur:</label>
      <input type="text" class="form-control" id="username" name="username" placeholder="Entrer un nom d'utilisateur">
    </div>
    <div class="form-group">
      <label for="password">Mot de passe:</label>
      <input type="password" class="form-control" id="password" name="password" placeholder="Entrer un mot de passe">
    </div>
    <div class="form-group">
      <input type="password" class="form-control" id="password_repeat" name="password_repeat" placeholder="Répéter le mot de passe">
    </div>
    <button type="submit" class="btn btn-default">Valider</button>
  </form>
</div>

</body>
</html>