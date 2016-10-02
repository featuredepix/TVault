<?php

require("config.php");

try {
  $connection = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $password);
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $query = $connection->query("SELECT * FROM library");

  $shows = array();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);

  foreach($result as $row){
    array_push($shows, $row['show_id']);
  }

  return implode(',',$shows);
} catch(PDOException $e){
  return "";
}

?>
