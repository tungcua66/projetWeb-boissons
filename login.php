<?php
  /*   display all php errors   */
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  session_start();

  if (isset($_GET["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    if (isset($username) && isset($password)) {
      $database_boissons = new sqlite3("boissons.sqlite", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
      if(!$database_boissons){
        echo $database_boissons->lastErrorMsg();
      }
      $sql_query = $database_boissons->query("select user_id, password_hash from login where username like \"" . $username . "\";");
      if ($row = $sql_query->fetchArray(SQLITE3_ASSOC)) {
        $user_id = $row["user_id"];
        $password_hash = $row["password_hash"];
        if ($password_hash == hash("sha512", trim($password))) {
          $_SESSION["username"] = $username;
          $_SESSION["user_id"] = $user_id;
          header("Location: index.php");    
        } else {
          echo "Wrong Password";
        }
      } else {
          echo "L'utilisateur " . $username . " n'existe pas !\n";
      }
      $database_boissons->close();
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Connexion</title>
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
  <h2>Se connecter <i class="fas fa-user-circle"></i></h2>
  <a href="index.php" class="btn btn-primary">Accueil <i class="fas fa-home"></i></a>
  <form role="form" method="post" action="login.php?login=true">
    <div class="form-group">
      <label for="username">Utilisateur:</label>
      <input type="text" class="form-control" id="username" name="username" placeholder="Entrer un nom d'utilisateur">
    </div>
    <div class="form-group">
      <label for="password">Mot de passe:</label>
      <input type="password" class="form-control" id="password" name="password" placeholder="Enter un mot de passe">
    </div>
    <button type="submit" class="btn btn-default">Valider</button>
  </form>
</div>

</body>
</html>