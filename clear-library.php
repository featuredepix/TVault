<?php

require("config.php");

try {
  $connection = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $password);
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $query = $connection->prepare("TRUNCATE TABLE library");
  $query->execute();

  header("Location: /?cleared");
  exit();
} catch(PDOException $e){
  echo $e->getMessage();
}

?>
