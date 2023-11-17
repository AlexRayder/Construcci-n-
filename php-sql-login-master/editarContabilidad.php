<?php
require_once 'includes/encabezado.php';
require_once 'includes/conexion.php';

// Inicializar variables
$nombre = '';
$cantidad = '';
$costoUnitario = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar los datos del formulario para actualizar registros
    $id = $_POST['id_material'];
    $nombre = $_POST['nombre_material'];
    $idRe = $_POST['id_registro'];
    $cantidad = $_POST["cantidad_utilizada"];
    $costoUnitario = $_POST["costo_unitario"];

    // Resto del código para actualizar registros
    $sql_actualizar = "UPDATE materiales m
                       JOIN registros_contabilidad r ON m.id_material = r.id_material
                       SET m.nombre_material='$nombre',
                           r.cantidad_utilizada='$cantidad',
                           r.costo_unitario='$costoUnitario'
                       WHERE m.id_material='$id' AND r.id_registro='$idRe'";

    $resultado_actualizar = mysqli_query($db, $sql_actualizar);

    if ($resultado_actualizar) {
        echo "<script language='JavaScript'>
                Swal.fire({
                    title: '¡Registro Actualizado!',
                    text: '¡Actualizaste Correctamente!',
                    icon: 'success'
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = 'registroContabilidad.php';
                    }
                  });
                </script>";
    } else {
        echo "<script language='JavaScript'>
        Swal.fire({
            title: '¡Error al Actualizar!',
            text: '¡No se actualizaron los datos!',
            icon: 'error'
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = 'registroContabilidad.php';
            }
          });
        </script>";
    }
    mysqli_close($db);
} else {
    // Recuperar datos para prellenar el formulario
    $id = isset($_GET['id_material']) ? $_GET['id_material'] : null;
    $idRe = isset($_GET['id_registro']) ? $_GET['id_registro'] : null;

    if ($id && $idRe) {
        // Consulta SQL para recuperar datos de ambas tablas
        $sql_datos = "SELECT m.id_material, m.nombre_material, r.id_registro, r.cantidad_utilizada, r.costo_unitario
                      FROM materiales m
                      JOIN registros_contabilidad r ON m.id_material = r.id_material
                      WHERE m.id_material = '$id' AND r.id_registro = '$idRe'";

        $resultado_datos = mysqli_query($db, $sql_datos);

        if ($resultado_datos) {
            $row = mysqli_fetch_assoc($resultado_datos);

            $nombre = $row["nombre_material"];
            $cantidad = $row["cantidad_utilizada"];
            $costoUnitario = $row["costo_unitario"];
        } else {
            die(mysqli_error($db));
        }

        mysqli_close($db);
    } else {
        // Manejar el caso en que $id o $idRe no esté establecido
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Editar</title>
</head>

<body>
    <div class="container">
        <h1>Editar</h1>
        <form method="post" action="<?= $_SERVER["PHP_SELF"]; ?>">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" name="nombre_material" placeholder="gasolina"
                    value="<?php echo $nombre; ?>">
                <label for="floatingInput">Nombre Material</label>
            </div>
            <div class="form-floating">
                <input type="number" class="form-control" name="cantidad_utilizada" id="floatinglugar"
                    placeholder="lugar" value="<?php echo $cantidad; ?>">
                <label for="floatinglugar">Cantidad</label>
            </div>
            <div class="form-floating">
                <input type="number" class="form-control" name="costo_unitario" id="floatinglugar" placeholder="lugar"
                value="<?php echo $costoUnitario; ?>">
                <label for="floatinglugar">Costo Unitario</label>
            </div>

            <input type="hidden" name="id_material" value="<?php echo $id; ?>">
            <input type="hidden" name="id_registro" value="<?php echo $idRe; ?>">

            <div>
                <input type="submit" name="enviar" value="Actualizar">
                <a href="registroContabilidad.php">Regresar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

<?php
require_once 'includes/inicio.php';
?>

</html>
