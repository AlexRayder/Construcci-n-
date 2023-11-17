<?php
require_once 'includes/encabezado.php';
require_once 'includes/conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombreMaterial = $_POST['nombre_material'];
    $cantidadMaterial = $_POST['cantidad'];

    // Ajustar formato de fecha de ingreso
    $fechaIngreso = date('Y-m-d H:i:s', strtotime($_POST['fecha_ingreso'] . ' 00:00:00'));

    $lugarAlmacenamiento = $_POST['lugar_almacenamiento'];

    $tipoMovimiento = $_POST['tipo_movimiento'];
    $cantidadMovimiento = ($tipoMovimiento === 'Entrada') ? $cantidadMaterial : -$cantidadMaterial;

    // Ajustar formato de fecha de movimiento
    $fechaMovimiento = ($tipoMovimiento === 'Salida') ? date('Y-m-d H:i:s', strtotime($_POST['fecha_movimiento'] . ' 00:00:00')) : $fechaIngreso;

    // Iniciar transacción para asegurar la consistencia de los datos
    mysqli_begin_transaction($db);

    try {
        // Verificar si el material ya existe en la tabla 'inventario'
        $sqlCheckMaterial = "SELECT id, cantidad, fecha_ingreso FROM inventario WHERE nombre_material = ?";
        $stmtCheckMaterial = mysqli_prepare($db, $sqlCheckMaterial);

        if (!$stmtCheckMaterial) {
            throw new Exception("Error en la preparación de la consulta 'verificar material': " . mysqli_error($db));
        }

        mysqli_stmt_bind_param($stmtCheckMaterial, "s", $nombreMaterial);
        mysqli_stmt_execute($stmtCheckMaterial);
        mysqli_stmt_store_result($stmtCheckMaterial);

        if (mysqli_stmt_num_rows($stmtCheckMaterial) > 0) {
            // El material ya existe, obtén el ID y la cantidad actual
            mysqli_stmt_bind_result($stmtCheckMaterial, $idInventarioExistente, $cantidadExistente, $fechaIngresoExistente);
            mysqli_stmt_fetch($stmtCheckMaterial);

            // Actualizar la cantidad en la tabla 'inventario'
            $sqlUpdateInventario = "UPDATE inventario SET cantidad = cantidad + ?, fecha_ingreso = ? WHERE id = ?";
            $stmtUpdateInventario = mysqli_prepare($db, $sqlUpdateInventario);

            if (!$stmtUpdateInventario) {
                throw new Exception("Error en la preparación de la consulta de actualización en 'inventario': " . mysqli_error($db));
            }

            $nuevaFechaIngreso = ($tipoMovimiento === 'Entrada') ? $fechaIngresoExistente : $fechaIngreso;
            mysqli_stmt_bind_param($stmtUpdateInventario, "ssi", $cantidadMovimiento, $nuevaFechaIngreso, $idInventarioExistente);
            $resultUpdateInventario = mysqli_stmt_execute($stmtUpdateInventario);

            // Verificar si la actualización en 'inventario' fue exitosa
            if (!$resultUpdateInventario) {
                throw new Exception("Error al actualizar la cantidad en la tabla 'inventario': " . mysqli_error($db));
            }

            echo "Cantidad actualizada en 'inventario' para el material existente. ID del material: $idInventarioExistente<br>";
            $idInventario = $idInventarioExistente;
        } else {
            // El material no existe, proceder con la inserción en 'inventario'
            $sqlInventario = "INSERT INTO inventario (nombre_material, cantidad, fecha_ingreso, lugar_almacenamiento) VALUES (?, ?, ?, ?)";
            $stmtInventario = mysqli_prepare($db, $sqlInventario);

            if (!$stmtInventario) {
                throw new Exception("Error en la preparación de la consulta 'inventario': " . mysqli_error($db));
            }

            mysqli_stmt_bind_param($stmtInventario, "siss", $nombreMaterial, $cantidadMovimiento, $fechaIngreso, $lugarAlmacenamiento);
            $resultInventario = mysqli_stmt_execute($stmtInventario);

            // Verificar si la inserción en 'inventario' fue exitosa
            if (!$resultInventario) {
                throw new Exception("Error al insertar en la tabla 'inventario': " . mysqli_error($db));
            }

            // Obtener el ID del material recién insertado
            $idInventario = mysqli_insert_id($db);

            echo "Registro en 'inventario' insertado con éxito. ID del material: $idInventario<br>";
          

        }

        // Insertar el registro en la tabla 'movimientos'
        $sqlMovimientos = "INSERT INTO movimientos (id_inventario, tipo_movimiento, cantidad, fecha_movimiento) VALUES (?, ?, ?, ?)";
        $stmtMovimientos = mysqli_prepare($db, $sqlMovimientos);

        if (!$stmtMovimientos) {
            throw new Exception("Error en la preparación de la consulta 'movimientos': " . mysqli_error($db));
        }

        mysqli_stmt_bind_param($stmtMovimientos, "isss", $idInventario, $tipoMovimiento, $cantidadMaterial, $fechaMovimiento);
        $resultMovimientos = mysqli_stmt_execute($stmtMovimientos);

        // Verificar si la inserción en 'movimientos' fue exitosa
        if (!$resultMovimientos) {
            throw new Exception("Error al insertar en la tabla 'movimientos': " . mysqli_error($db));
        }

        echo "Registro en 'movimientos' insertado con éxito<br>";

        // Confirmar la transacción después de todas las operaciones
        if (!mysqli_commit($db)) {
            throw new Exception("Error al confirmar la transacción: " . mysqli_error($db));
        }

        $mensaje = "Registros insertados y actualizados con éxito";
    } catch (Exception $e) {
        // Revertir la transacción si algo sale mal
        mysqli_rollback($db);

        echo "Error: " . $e->getMessage(); // Muestra el mensaje de error específico.
        echo "<br>";

        echo "Datos del formulario:<br>";
        var_dump($_POST); // Muestra los datos del formulario.

        $mensaje = "Error al insertar registros: " . $e->getMessage();
    }
}

// Consulta para obtener los datos de la base de datos
$sqlConsulta = "SELECT * FROM inventario";
$resultConsulta = mysqli_query($db, $sqlConsulta);

if (!$resultConsulta) {
    die("Error al obtener datos: " . mysqli_error($db));
}

// Cerrar conexión después de obtener datos
mysqli_close($db);
?>


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bodega</title>

<!-- Agregar enlaces a las bibliotecas de DataTables y jQuery -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<script>
    // Función para mostrar u ocultar el campo de fecha de ingreso según el tipo de movimiento
    function mostrarOcultarFechaIngreso() {
        var tipoMovimiento = document.getElementById("tipo_movimiento").value;
        var fechaIngreso = document.getElementById("fecha_ingreso_div");
        var fechaMovimiento = document.getElementById("fecha_movimiento_div");

        if (tipoMovimiento === "Entrada") {
            fechaIngreso.style.display = "block";
            fechaMovimiento.style.display = "none";
        } else {
            fechaIngreso.style.display = "none";
            fechaMovimiento.style.display = "block";
        }
    }

    // Inicializar DataTable
    $(document).ready(function () {
        $('#tablaInventario').DataTable();
    });

    // Llamar a la función al cargar la página y cuando cambie el tipo de movimiento
    window.onload = mostrarOcultarFechaIngreso;
</script>

<form method="POST" action="">
    <!-- Campos para la tabla de inventario -->
    <label for="nombre_material">Nombre del Material:</label>
    <input type="text" name="nombre_material" id="nombre_material" required><br>

    <label for="cantidad_material">Cantidad de Material:</label>
    <input type="number" name="cantidad" id="cantidad_material" required><br>

    <label for="tipo_movimiento">Tipo de Movimiento:</label>
    <select name="tipo_movimiento" id="tipo_movimiento" onchange="mostrarOcultarFechaIngreso()" required>
        <option value="Entrada">Entrada</option>
        <option value="Salida">Salida</option>
    </select><br>

    <!-- Campos para la tabla de movimientos -->
    <!-- Estos campos se llenan automáticamente en función de los datos ingresados arriba -->

    <div id="fecha_ingreso_div">
        <label for="fecha_ingreso">Fecha de Ingreso:</label>
        <input type="date" name="fecha_ingreso" id="fecha_ingreso"><br>
    </div>

    <div id="fecha_movimiento_div" style="display:none;">
        <label for="fecha_movimiento">Fecha de Movimiento:</label>
        <input type="date" name="fecha_movimiento" id="fecha_movimiento"><br>
    </div>

    <label for="lugar_almacenamiento">Lugar de Almacenamiento:</label>
    <input type="text" name="lugar_almacenamiento" id="lugar_almacenamiento" required><br>

    <input type="submit" value="Registrar">
</form>


<div class="container">
<h2>Tabla de Inventario</h2>

<!-- Agregar un contenedor para la tabla -->
<table id="tablaInventario">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre del Material</th>
            <th>Cantidad</th>
            <th>Fecha de Ingreso</th>
            <th>Lugar de Almacenamiento</th>
            <th style='text-align:center;'>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Mostrar datos en la tabla
        while ($row = mysqli_fetch_assoc($resultConsulta)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nombre_material']}</td>";
            echo "<td>{$row['cantidad']}</td>";
            echo "<td>{$row['fecha_ingreso']}</td>";
            echo "<td>{$row['lugar_almacenamiento']}</td>";
            echo "<td style='text-align:center;'> 
            <a href='editarBodega.php?id=".$row['id']."'><input type='submit' name='Submit' value='Editar'></a>
            <input type='submit' name='Submit' value='Eliminar' onclick=\"eliminarBodega(".$row['id'].")\">
          </td>";
    
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
</div>
<script src="js/eliminar.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
require_once 'includes/inicio.php';
?>