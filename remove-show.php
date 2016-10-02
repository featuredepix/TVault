<?php

require("config.php");

try {
  $connection = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $password);
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $query = $connection->prepare("DELETE FROM library WHERE show_id=?");
  $query->execute(array(intval(trim($_GET['id']))));

  header("Location: /?removed");
  exit();
} catch(PDOException $e){
  echo $e->getMessage();
}

?>
