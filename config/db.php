<?php

$servername = "localhost";
$username = "mediturnos_app";
$password = "654987";
$dbname = "mediturnos";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Conexion Exitosa";
} catch(PDOException $e) {
  echo "Conexion Fallada: " . $e->getMessage();
}

?>