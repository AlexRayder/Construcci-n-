<?php
require_once 'includes/encabezado.php';
require_once 'includes/conexion.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Editar Apartamento</title>
</head>

<body>
    <?php
    if (isset($_POST['enviar'])) {
        $id = $_POST['id_apartamento'];
        $direccion = $_POST['direccion'];
        $persona = $_POST["persona_encargada"];
        $estado = $_POST["estado"];
        $sql = "update apartamento set direccion='" . $direccion . "', persona_encargada='" . $persona . "', estado='" . $estado . "' where id_apartamento='" . $id . "'";
        $resultado = mysqli_query($db, $sql);

        if ($resultado) {
            echo "<script language='JavaScript'>
            Swal.fire({
                title: '¡Registro Actualizado!',
                text: '¡Actualizaste Correctamente!',
                icon: 'success'
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location.href = 'apartamentos.php';
                }
              });
            </script>";
        } else {
            echo "<script language='JavaScript'>
            Swal.fire({
                title: '¡Error al Actualizar!',
                text: '¡verifica correctamente los datos:D!',
                icon: 'error'
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location.href = 'editarApartamento.php';
                }
              });
            </script>";
        }
        mysqli_close($db);

    } else {
        $id = $_GET['id'];
        $sql = "SELECT * from  apartamento where id_apartamento='" . $id . "'";
        $resultado = mysqli_query($db, $sql);
    
        $row = mysqli_fetch_assoc($resultado);
        $direccion = $row["direccion"];
        $persona = $row["persona_encargada"];
        $estado = $row["estado"];  // Agrega esta línea para inicializar $estado
    
        mysqli_close($db);
    }

    ?>
    <div class="container">
        <h1>Editar Apartamento</h1>
        <form method="post" action="<?= $_SERVER["PHP_SELF"]; ?>">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" name="direccion" placeholder="call or cra"
                    value="<?php echo $direccion; ?>">
                <label for="floatingInput">Direccion</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control mb-3" name="persona_encargada" id="floatinglugar"
                    placeholder="Persona" value="<?php echo $persona; ?>">
                <label for="floatinglugar">Persona Encargada</label>
            </div>
            <label for="estado">Nuevo Estado:</label>
            <select name="estado"  class="form-select form-select-lg mb-3" aria-label="Large select example">
                <option value="Desarrollo" <?php echo ($estado == 'Desarrollo') ? 'selected' : ''; ?>>Desarrollo
                </option>
                <option value="En Construccion" <?php echo ($estado == 'En Construccion') ? 'selected' : ''; ?>>En
                    Construcción</option>
                <option value="Finalizado" <?php echo ($estado == 'Finalizado') ? 'selected' : ''; ?>>Finalizado</option>
            </select>

            <input type="hidden" name="id_apartamento" value="<?php echo $id; ?>">
            <div>
                <input type="submit" name="enviar" value="Actualizar">
                <a href="apartamentos.php">Regresar</a>
            </div>
        </form>
    </div>



</body>
<?php
require_once 'includes/inicio.php';

?>

</html>