<?php

require("config.php");

try {
  $connection = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $password);
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $query = $connection->prepare("INSERT INTO library(show_id) VALUES (?) ON DUPLICATE KEY UPDATE show_id=show_id");
  $query->execute(array(intval(trim($_GET['id']))));

  header("Location: /?added");
  exit();
} catch(PDOException $e){
  echo $e->getMessage();
}

?>
