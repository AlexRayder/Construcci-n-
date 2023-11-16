<?php
require_once 'includes/encabezado.php';
require_once 'includes/conexion.php';

$sql_apartamentos = "SELECT id_apartamento, direccion, persona_encargada, fecha_inicio_contruccion, fecha_fin_construccion, estado FROM Apartamento";
$result_apartamentos = $db->query($sql_apartamentos);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formulario de Registro de Apartamento
    if (isset($_POST['registrar_apartamento'])) {
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $estado = $_POST['estado'];

        $sql_apartamento = "INSERT INTO Apartamento (direccion, persona_encargada, fecha_inicio_contruccion, fecha_fin_construccion, estado)
                            VALUES ('$direccion', '$nombre', '$fecha_inicio', '$fecha_fin', '$estado')";

        if ($db->query($sql_apartamento) === TRUE) {
            echo "Registro de apartamento exitoso";


        } else {
            echo "Error: " . $sql_apartamento . "<br>" . $db->error;
        }
    }

    // Formulario de Registro de Novedad en el Historial
    if (isset($_POST['registrar_novedad'])) {
        $direccion_apartamento = $_POST['direccion_apartamento'];
        $fecha_novedad = date("Y-m-d");  
        $novedad = $_POST['novedad'];
        $nombre_usuario = $_POST['nombre_usuario'];
        $cedula_usuario = $_POST['cedula_usuario'];
        $usuario_realizo = "$cedula_usuario - $nombre_usuario";
    
        // Consulta para obtener el ID del apartamento
        $sql_id_apartamento = "SELECT id_apartamento FROM Apartamento WHERE direccion = '$direccion_apartamento'";
        $result_id_apartamento = $db->query($sql_id_apartamento);
    
        if ($result_id_apartamento->num_rows > 0) {
            $row = $result_id_apartamento->fetch_assoc();
            $id_apartamento = $row['id_apartamento'];
    
            // Inserción de datos en la tabla Historial
            $sql_historial = "INSERT INTO Historial (id_apartamento, fecha_registro, avances_novedades, usuario_realizo)
                              VALUES ('$id_apartamento', '$fecha_novedad', '$novedad', '$usuario_realizo')";
    
            if ($db->query($sql_historial) === TRUE) {
                echo "Registro de novedad exitoso";
                // Redirigir al usuario a otra página o mostrar un mensaje de éxito
            } else {
                echo "Error: " . $sql_historial . "<br>" . $db->error;
            }
        } else {
            echo "No se encontró un apartamento con la dirección proporcionada.";
        }
    }
}
?>


<h2>Registro de Apartamento</h2>
<form action="" method="post">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" required>

    <label for="direccion">Dirección:</label>
    <input type="text" name="direccion" required>

    <label for="fecha_inicio">Fecha de Inicio de Construcción:</label>
    <input type="date" name="fecha_inicio" required>

    <label for="fecha_fin">Fecha de Fin de Construcción:</label>
    <input type="date" name="fecha_fin" required>

    <label for="estado">Estado:</label>
    <select name="estado" required>
        <option value="Desarrollo">En Desarrollo</option>
        <option value="En Construccion">En Construcción</option>
        <option value="Finalizado">Finalizado</option>
    </select>

    <input type="submit" name="registrar_apartamento" value="Registrar Apartamento">
</form>

<h2>Registro de Novedad en Historial</h2>
<form action="" method="post">
    <label for="direccion_apartamento">Dirección del Apartamento:</label>
    <select name="direccion_apartamento" required>
        <?php
        while ($row = $result_apartamentos->fetch_assoc()) {
            echo "<option value='{$row['direccion']}'>{$row['direccion']}</option>";
        }
        ?>
    </select>

    <label for="novedad">Novedad:</label>
    <textarea name="novedad" rows="4" cols="50"></textarea>

    <label for="nombre_usuario">Nombre del Usuario:</label>
    <input type="text" name="nombre_usuario" required>

    <label for="cedula_usuario">Número de Cédula del Usuario:</label>
    <input type="text" name="cedula_usuario" required>

    <input type="submit" name="registrar_novedad" value="Registrar Novedad">
</form>

<h2>Consulta y Actualización de Apartamentos</h2>
<!-- Formulario de Consulta -->
<form action="" method="post">
    <label for="apartamento_id">Seleccionar Apartamento:</label>
    <select name="apartamento_id" required>
        <?php
        $result_apartamentos->data_seek(0); // Reiniciar el puntero del resultado
        while ($row = $result_apartamentos->fetch_assoc()) {
            echo "<option value='{$row['id_apartamento']}'>{$row['direccion']}</option>";
        }
        ?>
    </select>
    <input type="submit" name="consultar_apartamento" value="Consultar">
</form>

<?php
// Procesar la consulta y mostrar la información del apartamento seleccionado
if (isset($_POST['consultar_apartamento'])) {
    $apartamento_id = $_POST['apartamento_id'];

    $sql_info_apartamento = "SELECT * FROM Apartamento WHERE id_apartamento = $apartamento_id";
    $result_info_apartamento = $db->query($sql_info_apartamento);

    if ($result_info_apartamento->num_rows > 0) {
        $apartamento = $result_info_apartamento->fetch_assoc();
        ?>
        <!-- Mostrar la información del apartamento -->
        <h3>Información del Apartamento</h3>
        <p>ID: <?php echo $apartamento['id_apartamento']; ?></p>
        <p>Dirección: <?php echo $apartamento['direccion']; ?></p>
        <p>Persona Encargada: <?php echo $apartamento['persona_encargada']; ?></p>
        <p>Fecha de Inicio de Construcción: <?php echo date("Y-m-d", strtotime($apartamento['fecha_inicio_contruccion'])); ?></p>
        <p>Fecha de Fin de Construcción: <?php echo date("Y-m-d", strtotime($apartamento['fecha_fin_construccion'])); ?></p>
        <p>Estado: <?php echo $apartamento['estado']; ?></p>

        <!-- Formulario de Actualización de Estado -->
        <form action="" method="post">
            <input type="hidden" name="apartamento_id" value="<?php echo $apartamento['id_apartamento']; ?>">
            <label for="nuevo_estado">Nuevo Estado:</label>
            <select name="nuevo_estado" required>
                <option value="Desarrollo">En Desarrollo</option>
                <option value="En Construccion">En Construcción</option>
                <option value="Finalizado">Finalizado</option>
            </select>
            <input type="submit" name="actualizar_estado" value="Actualizar Estado">
        </form>
        <?php
    } else {
        echo "No se encontró un apartamento con el ID proporcionado.";
    }
}

// Procesar la actualización de estado
if (isset($_POST['actualizar_estado'])) {
    $apartamento_id = $_POST['apartamento_id'];
    $nuevo_estado = $_POST['nuevo_estado'];

    $sql_actualizar_estado = "UPDATE Apartamento SET estado = '$nuevo_estado' WHERE id_apartamento = $apartamento_id";
    if ($db->query($sql_actualizar_estado) === TRUE) {
        echo "Estado actualizado exitosamente";
    } else {
        echo "Error al actualizar el estado: " . $db->error;
    }
}
?>

<h2>Lista de Apartamentos</h2>
<!-- Agrega una tabla con un identificador único -->
<table id="tablaApartamentos">
    <thead>
        <tr>
            <th>ID</th>
            <th>Dirección</th>
            <th>Persona Encargada</th>
            <th>Fecha de Inicio</th>
            <th>Fecha de Fin</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result_apartamentos->data_seek(0); // Reiniciar el puntero del resultado
        while ($row = $result_apartamentos->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id_apartamento']}</td>";
            echo "<td>{$row['direccion']}</td>";
            echo "<td>{$row['persona_encargada']}</td>";
            echo "<td>" . date("Y-m-d", strtotime($row['fecha_inicio_contruccion'])) . "</td>";
            echo "<td>" . date("Y-m-d", strtotime($row['fecha_fin_construccion'])) . "</td>";
            echo "<td>{$row['estado']}</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<!-- Agrega el archivo JavaScript de DataTables y su dependencia jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
    // Inicializa la tabla DataTables
    $(document).ready(function() {
        $('#tablaApartamentos').DataTable();
    });
</script>
<?php
require_once 'includes/inicio.php';
?>