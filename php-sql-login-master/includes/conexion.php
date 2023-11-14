<?php
$host = "localhost";
$user = "root";
$password = "";
$db_name = "construccion";

// Crear la conexión
$db = new mysqli($host, $user, $password, $db_name);

// Verificar la conexión
if ($db->connect_error) {
    die("Conexión fallida: " . $db->connect_error);
}
?>
