<?php
require_once 'includes/conexion.php';
require_once 'includes/encabezado.php';


// Inicializar variables
$materiales = [];
$registrosContabilidad = [];

// Manejo del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['actualizar_estado'])) {
        $id_material_actualizar = $_POST['id_material'];
        $nuevo_estado = $_POST['nuevo_estado'];

        // Actualizar el estado del material
        $sql_actualizar_estado = "UPDATE materiales SET estado = '$nuevo_estado' WHERE id_material = $id_material_actualizar";

        if ($db->query($sql_actualizar_estado) === TRUE) {
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
            echo "Error al actualizar el estado: " . $db->error . "<br>";
        }
    } else {
        // Recuperar los datos del formulario
        $nombre_material = strtoupper($_POST["nombre_material"]); // Convertir a mayúsculas
        $estado = $_POST["estado"];

        // Verificar si el material ya existe
        $sql_check_material = "SELECT id_material FROM materiales WHERE nombre_material = '$nombre_material'";
        $result_check_material = $db->query($sql_check_material);

        // Insertar datos en la tabla 'materiales'
        if ($result_check_material->num_rows > 0) {
            echo "<script language='JavaScript'>
                    Swal.fire({
                        title: '¡Error!',
                        text: '¡Este Material Ya Existe!',
                        icon: 'error'
                      }).then((result) => {
                        if (result.isConfirmed) {
                          window.location.href = 'registroContabilidad.php';
                        }
                      });
                    </script>";
        } else {
            $sql_material = "INSERT INTO materiales (nombre_material, estado) 
                             VALUES ('$nombre_material', '$estado')";

            if ($db->query($sql_material) === TRUE) {
                echo "<script language='JavaScript'>
                    Swal.fire({
                        title: '¡Registro Exitoso!',
                        text: '¡Agregaste Correctamente este Material!',
                        icon: 'success'
                      }).then((result) => {
                        if (result.isConfirmed) {
                          window.location.href = 'restroContabilidad.php';
                        }
                      });
                    </script>";

                // Recuperar el ID del material recién insertado
                $id_material = $db->insert_id;

                // Recuperar los datos del formulario de la compra
                $cantidad_utilizada = $_POST["cantidad_utilizada"];
                $fecha_registro = $_POST["fecha_registro"];
                $costo_unitario = $_POST["costo_unitario"];
                $costo_total = $cantidad_utilizada * $costo_unitario;

                // Insertar datos en la tabla 'registros_contabilidad'
                $sql_compra = "INSERT INTO registros_contabilidad (id_material, cantidad_utilizada, fecha_registro, costo_unitario, costo_total) 
                               VALUES ('$id_material', '$cantidad_utilizada', '$fecha_registro', '$costo_unitario', '$costo_total')";

                if ($db->query($sql_compra) === TRUE) {
                    echo "<script language='JavaScript'>
                    Swal.fire({
                        title: '¡Compra Registrada!',
                        text: '¡Exitosamente Agregaste esta Compra!',
                        icon: 'success'
                      }).then((result) => {
                        if (result.isConfirmed) {
                          window.location.href = 'registroContabilidad.php';
                        }
                      });
                    </script>";
                } else {
                    echo "Error al registrar la compra: " . $db->error . "<br>";
                }
            } else {
                echo "Error al agregar el material: " . $db->error . "<br>";
            }
        }
    }
    $sqlMaterialSeleccionado = "SELECT * FROM materiales WHERE id_material = ?";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Manejo del formulario de actualización de estado
        if (isset($_POST['actualizar_estado'])) {
            $id_material_actualizar = $_POST['id_material'];
            $nuevo_estado = $_POST['nuevo_estado'];
            $nuevo_nombre = isset($_POST['nuevo_nombre']) ? $_POST['nuevo_nombre'] : null;

            // Actualizar el estado del material
            $sql_actualizar_estado = "UPDATE materiales SET estado = ? WHERE id_material = ?";
            $stmt_actualizar_estado = $db->prepare($sql_actualizar_estado);
            $stmt_actualizar_estado->bind_param("si", $nuevo_estado, $id_material_actualizar);

            if ($stmt_actualizar_estado->execute()) {
                echo "Estado actualizado con éxito.<br>";

                // Actualizar nombre si se proporciona
                if (!is_null($nuevo_nombre)) {
                    // Obtener datos actuales del material
                    $stmt_material_seleccionado = $db->prepare($sqlMaterialSeleccionado);
                    $stmt_material_seleccionado->bind_param("i", $id_material_actualizar);
                    $stmt_material_seleccionado->execute();
                    $result_material_seleccionado = $stmt_material_seleccionado->get_result();
                    $datos_material = $result_material_seleccionado->fetch_assoc();

                    // Actualizar nombre si se proporciona
                    $nuevo_nombre = !empty($nuevo_nombre) ? $nuevo_nombre : $datos_material['nombre_material'];

                    // Actualizar nombre y estado
                    $sql_actualizar_datos = "UPDATE materiales SET nombre_material = ?, estado = ? WHERE id_material = ?";
                    $stmt_actualizar_datos = $db->prepare($sql_actualizar_datos);
                    $stmt_actualizar_datos->bind_param("ssi", $nuevo_nombre, $nuevo_estado, $id_material_actualizar);

                    if ($stmt_actualizar_datos->execute()) {
                        echo "<script language='JavaScript'>
                        Swal.fire({
                            title: '¡Datos Actualizados!',
                            text: '¡Actualizaste Correctamente!',
                            icon: 'success'
                          }).then((result) => {
                            if (result.isConfirmed) {
                              window.location.href = 'registroContabilidad.php';
                            }
                          });
                        </script>";
                    } else {
                        echo "Error al actualizar los datos: " . $stmt_actualizar_datos->error . "<br>";
                    }
                }
            } else {
                echo "Error al actualizar el estado: " . $stmt_actualizar_estado->error . "<br>";
            }
        }
    }


}


// Consulta para obtener datos de la tabla 'materiales'
$sqlConsulta = "SELECT * FROM materiales";
$resultConsulta = $db->query($sqlConsulta);

// Consulta para obtener datos de la tabla 'registros_contabilidad'
$sqlRegistro = "SELECT * FROM registros_contabilidad";
$resultConsulta2 = $db->query($sqlRegistro);

// Almacenar resultados en arrays
while ($row = mysqli_fetch_assoc($resultConsulta)) {
    $materiales[] = $row;
}

// Reiniciar el puntero para volver a leer los resultados
$resultConsulta->data_seek(0);

while ($row = mysqli_fetch_assoc($resultConsulta2)) {
    $registrosContabilidad[] = $row;
}

// Cerrar la conexión
$db->close();


?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();

    $rol = 'id_usuario';

    // Verificar el rol del usuario
    if ($rol === 'Administrador') {
        $_SESSION['mostrar_formulario'] = true;
    } else {
        $_SESSION['mostrar_formulario'] = false;
    }
}


?>

<div class="container mt-5">
    <h1>Registro Compra</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        <div class="form-floating mb-3">
            <input type="text" name="nombre_material" class="form-control" required placeholder="name@example.com">
            <label for="nombre_material">Nombre del Material:</label>
        </div>

        <div class="form-floating mb-3">
            <select name="estado" class="form-select" required placeholder="name@example.com">
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
            </select>
            <label for="estado">Estado:</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="cantidad_utilizada" class="form-control" required placeholder="name@example.com">
            <label for="cantidad_utilizada">Cantidad :</label>
        </div>

        <div class="form-floating mb-3">

            <input type="date" name="fecha_registro" class="form-control" required placeholder="name@example.com">
            <label for="fecha_registro">Fecha de Registro:</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="costo_unitario" class="form-control" required placeholder="name@example.com">
            <label for="costo_unitario">Costo Unitario:</label>
        </div>
        <div>
            <input type="submit" class="btn btn-success" value="Registrar Compra">
        </div>
    </form>
</div>
<hr>


<!-- Agregar enlaces a las bibliotecas de DataTables y jQuery -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>


<?php
if ($rol === 'Administrador') {
    ?>
    <div class="container mt-5">
        <h1>Actualizar nombre y estado</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-floating mb-3">

                <select name="id_material" class="form-select" required placeholder="name@example.com">
                    <?php
                    foreach ($materiales as $row_material) {
                        // Mostrar solo los materiales inactivos
                        if ($row_material["estado"] === 'Inactivo') {
                            echo "<option value='{$row_material["id_material"]}'>{$row_material["nombre_material"]}</option>";
                        }
                    }
                    ?>
                </select>
                <label for="id_material">Seleccione un material inactivo:</label>
            </div>
            <div class="form-floating mb-3">

                <select name="nuevo_estado" class="form-select" required placeholder="name@example.com">
                    <option value="Activo">Activo</option>
                </select>
                <label for="nuevo_estado">Nuevo Estado:</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" name="nuevo_nombre" class="form-control" placeholder="name@example.com">
                <label for="nuevo_nombre">Nuevo Nombre:</label>
            </div>
            <div>
                <input type="submit" class="btn btn-success" name="actualizar_estado" value="Actualizar Estado">
            </div>
        </form>
    </div>
    <?php
}
?>



<!-- Agrega un contenedor para la tabla -->
<div class="container">
    <table id="tablaInventario">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Material</th>
                <th>Estado</th>
                <th>Cantidad Utilizada</th>
                <th>Fecha de Registro</th>
                <th>Costo Unitario</th>
                <th>Costo Total</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalFilas = max(count($materiales), count($registrosContabilidad));

            for ($i = 0; $i < $totalFilas; $i++) {
                echo "<tr id='fila" . (!empty($materiales[$i]) ? $materiales[$i]['id_material'] : '') . "' class='" . (!empty($materiales[$i]) && $materiales[$i]['estado'] == 'Inactivo' ? 'inactivo' : '') . "'>";

                if (!empty($materiales[$i])) {
                    echo "<td>{$materiales[$i]['id_material']}</td>";
                    echo "<td>{$materiales[$i]['nombre_material']}</td>";
                    echo "<td>{$materiales[$i]['estado']}</td>";
                } else {
                    echo "<td></td><td></td><td></td>";
                }

                if (!empty($registrosContabilidad[$i])) {
                    echo "<td>{$registrosContabilidad[$i]['cantidad_utilizada']}</td>";
                    echo "<td>{$registrosContabilidad[$i]['fecha_registro']}</td>";
                    echo "<td>{$registrosContabilidad[$i]['costo_unitario']}</td>";
                    echo "<td>{$registrosContabilidad[$i]['costo_total']}</td>";
                } else {
                    echo "<td></td><td></td><td></td><td></td>";
                }

                echo "<td style='text-align:center;'>";

                if (!empty($materiales[$i]) && $materiales[$i]['estado'] == 'Activo') {
                    echo "<input type='submit' name='Submit' value='Eliminar' class='btn btn-danger' onclick=\"eliminarContabilidad(" . $materiales[$i]['id_material'] . ")\">";
                }

                echo "</td>";

                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<style>
    .inactivo {
        display: none;
    }
</style>

<script>
    // Inicializar DataTable
    $(document).ready(function () {
        $('#tablaInventario').DataTable();
    });
</script>
<script src="js/eliminar.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<?php require_once 'includes/inicio.php' ?>
<?php require_once 'includes/footer.php' ?>