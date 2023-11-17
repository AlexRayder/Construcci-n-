

<html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<?php
require_once 'includes/conexion.php';

$eliminar = isset($_POST['id']) ? $_POST['id'] : null;

if ($eliminar !== null) {
    $sentencia = $db->query("DELETE FROM inventario WHERE id=$eliminar");
   
}
?>



</html>