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
    <title>Editar Bodega</title>
</head>

<body>
    <?php
    if (isset($_POST['enviar'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre_material'];
        $lugar = $_POST["lugar_almacenamiento"];
        $sql = "update inventario set nombre_material='" . $nombre . "', lugar_almacenamiento='" . $lugar . "' where id='" . $id . "'";
        $resultado = mysqli_query($db, $sql);

        if ($resultado) {
            echo "<script language='JavaScript'>
                    Swal.fire({
                        title: '¡Registro Actualizado!',
                        text: '¡Actualizaste Correctamente!',
                        icon: 'success'
                      }).then((result) => {
                        if (result.isConfirmed) {
                          window.location.href = 'moduloBodega.php';
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
                  window.location.href = 'moduloBodega.php';
                }
              });
            </script>";
        }
        mysqli_close($db);

    } else {
        $id = $_GET['id'];
        $sql = "SELECT * from  inventario where id='" . $id . "'";
        $resultado = mysqli_query($db, $sql);

        $row = mysqli_fetch_assoc($resultado);
        $nombre = $row["nombre_material"];
        $lugar = $row["lugar_almacenamiento"];

        mysqli_close($db);

    }

    ?>
    <div class="container">
        <h1>Editar Bodega</h1>
        <form method="post" action="<?= $_SERVER["PHP_SELF"]; ?>">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" name="nombre_material" placeholder="gasolina"
                    value="<?php echo $nombre; ?>">
                <label for="floatingInput">Nombre Material</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="lugar_almacenamiento" id="floatinglugar"
                    placeholder="lugar" value="<?php echo $lugar; ?>">
                <label for="floatinglugar">Lugar Almacenamiento</label>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="col-md-6 mb-3"> 
                <input type="submit" class="btn btn-success" name="enviar" value="Actualizar">
                <a href="moduloBodega.php" class="btn btn-danger" >Regresar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</body>
<?php
require_once 'includes/inicio.php';

?>

</html>