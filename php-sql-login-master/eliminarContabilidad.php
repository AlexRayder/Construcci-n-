

<html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<?php
require_once 'includes/conexion.php';

$id_material = isset($_POST['id_material']) ? $_POST['id_material'] : null;

if ($id_material !== null) {
    
    $sql_inhabilitar_material = "UPDATE materiales SET estado = 'Inactivo' WHERE id_material = $id_material";

    if ($db->query($sql_inhabilitar_material) === TRUE) {
        echo "success"; 
    } else {
        echo "error"; 
    }
} else {
    echo "error"; 
}

$db->close();
?>
