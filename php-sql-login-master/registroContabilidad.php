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
            echo "Estado actualizado con éxito.<br>";
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
            echo "Material ya existe.<br>";
        } else {
            $sql_material = "INSERT INTO materiales (nombre_material, estado) 
                             VALUES ('$nombre_material', '$estado')";

            if ($db->query($sql_material) === TRUE) {
                echo "Material agregado con éxito.<br>";

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
                    echo "Compra registrada con éxito.";
                } else {
                    echo "Error al registrar la compra: " . $db->error . "<br>";
                }
            } else {
                echo "Error al agregar el material: " . $db->error . "<br>";
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

<!-- Formulario HTML -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="nombre_material">Nombre del Material:</label>
    <input type="text" name="nombre_material" required><br>

    <label for="estado">Estado:</label>
    <select name="estado">
        <option value="Activo">Activo</option>
        <option value="Inactivo">Inactivo</option>
    </select><br>

    <label for="cantidad_utilizada">Cantidad :</label>
    <input type="text" name="cantidad_utilizada" required><br>

    <label for="fecha_registro">Fecha de Registro:</label>
    <input type="date" name="fecha_registro" required><br>

    <label for="costo_unitario">Costo Unitario:</label>
    <input type="text" name="costo_unitario" required><br>

    <input type="submit" value="Registrar Compra">
</form>
<hr>


<!-- Agregar enlaces a las bibliotecas de DataTables y jQuery -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>


<!-- Formulario HTML de actualización de estado -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="id_material">Seleccione un material:</label>
    <select name="id_material">
        <?php
        foreach ($materiales as $row_material) {
            echo "<option value='{$row_material["id_material"]}'>{$row_material["nombre_material"]}</option>";
        }
        ?>
    </select><br>

    <label for="nuevo_estado">Nuevo Estado:</label>
    <select name="nuevo_estado">
        <option value="Activo">Activo</option>
        <option value="Inactivo">Inactivo</option>
    </select><br>

    <input type="submit" name="actualizar_estado" value="Actualizar Estado">
</form>


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

            </tr>
        </thead>
        <tbody>
            <?php
            // Utiliza los arrays almacenados para mostrar la tabla
            $totalFilas = max(count($materiales), count($registrosContabilidad));

            for ($i = 0; $i < $totalFilas; $i++) {
                echo "<tr>";

                // Mostramos los datos de materiales
                if (!empty($materiales[$i])) {
                    echo "<td>{$materiales[$i]['id_material']}</td>";
                    echo "<td>{$materiales[$i]['nombre_material']}</td>";
                    echo "<td>{$materiales[$i]['estado']}</td>";
                } else {
                    // Si no hay datos de materiales para esta fila, agregar celdas vacías
                    echo "<td></td><td></td><td></td>";
                }

                // Mostramos los datos de registros_contabilidad
                if (!empty($registrosContabilidad[$i])) {
                    echo "<td>{$registrosContabilidad[$i]['cantidad_utilizada']}</td>";
                    echo "<td>{$registrosContabilidad[$i]['fecha_registro']}</td>";
                    echo "<td>{$registrosContabilidad[$i]['costo_unitario']}</td>";
                    echo "<td>{$registrosContabilidad[$i]['costo_total']}</td>";
                } else {
                    // Si no hay datos de registros_contabilidad para esta fila, agregar celdas vacías
                    echo "<td></td><td></td><td></td><td></td>";
                }

                // Agregar más columnas según sea necesario
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    // Inicializar DataTable
    $(document).ready(function () {
        $('#tablaInventario').DataTable();
    });
</script>

<?php require_once 'includes/inicio.php' ?>
<?php require_once 'includes/footer.php' ?>