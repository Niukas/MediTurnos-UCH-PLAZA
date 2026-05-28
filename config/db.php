<?php

$servername = "localhost";
$username = "mediturnos_app";
$password = "654987";
$dbname = "mediturnos";

try {
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Conexion Fallada: " . $e->getMessage();
}

?>