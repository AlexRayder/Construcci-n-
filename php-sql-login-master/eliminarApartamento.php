<html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<?php
require_once 'includes/conexion.php';

$eliminar = isset($_POST['id_apartamento']) ? $_POST['id_apartamento'] : null;

if ($eliminar !== null) {
    $sentencia = $db->query("DELETE FROM apartamento WHERE id_apartamento=$eliminar");

}
?>



</html>