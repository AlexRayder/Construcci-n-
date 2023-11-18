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
            echo "<script language='JavaScript'>
                    Swal.fire({
                        title: '¡Registro Exitoso!',
                        text: '¡Agregaste Correctamente este Apartamento!',
                        icon: 'success'
                      }).then((result) => {
                        if (result.isConfirmed) {
                          window.location.href = 'apartamentos.php';
                        }
                      });
                    </script>";
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
                echo "<script language='JavaScript'>
                Swal.fire({
                    title: '¡Registro Exitoso!',
                    text: '¡Agregaste esta Novedad Correctamente!',
                    icon: 'success'
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = 'apartamentos.php';
                    }
                  });
                </script>";

            } else {
                echo "Error: " . $sql_historial . "<br>" . $db->error;
            }
        } else {
            echo "No se encontró un apartamento con la dirección proporcionada.";
        }
    }
}
?>

<div class="container mt-5">
    <h2>Registro de Apartamento</h2>
    <form action="" method="post">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="nombre" required placeholder="Nombre Encargado">
            <label for="nombre">Nombre Encargado:</label>

        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="direccion" required placeholder="calle24">
            <label for="direccion">Dirección:</label>

        </div>

        <div class="form-floating mb-3">
            <input type="date" class="form-control" name="fecha_inicio" required placeholder="01/01/2023">
            <label for="fecha_inicio">Fecha de Inicio de Construcción:</label>

        </div>

        <div class="form-floating mb-3">
            <input type="date" class="form-control" name="fecha_fin" required placeholder="01/01/2029">
            <label for="fecha_fin">Fecha de Fin de Construcción:</label>

        </div>

        <div class="form-floating mb-3">
            <select class="form-select" name="estado" required placeholder="inicio,fin,planacion">
                <option value="Desarrollo">En Desarrollo</option>
                <option value="En Construccion">En Construcción</option>
                <option value="Finalizado">Finalizado</option>
            </select>
            <label for="estado">Estado:</label>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-success" name="registrar_apartamento" value="Registrar Apartamento">
        </div>
    </form>
</div>
<hr>
<div class="container mt-5">
    <h2>Registro de Novedad en Historial</h2>
    <form action="" method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="form-floating">
                    <select class="form-select" name="direccion_apartamento" id="direccion_apartamento" required>
                        <?php
                        while ($row = $result_apartamentos->fetch_assoc()) {
                            echo "<option value='{$row['direccion']}'>{$row['direccion']}</option>";
                        }
                        ?>
                    </select>
                    <label for="direccion_apartamento">Dirección del Apartamento:</label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-floating">
                    <textarea class="form-control" name="novedad" id="novedad" rows="4" required placeholder="01/01/2029"></textarea>
                    <label for="novedad">Novedad:</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="form-floating">
                    <input type="text" class="form-control" name="nombre_usuario" id="nombre_usuario" required placeholder="01/01/2029">
                    <label for="nombre_usuario">Nombre del Usuario:</label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-floating">
                    <input type="text" class="form-control" name="cedula_usuario" id="cedula_usuario" required placeholder="01/01/2029">
                    <label for="cedula_usuario">Número de Cédula del Usuario:</label>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <input type="submit" class="btn btn-success" name="registrar_novedad" value="Registrar Novedad">
        </div>
    </form>
</div>



<!-- Agrega una tabla con un identificador único -->
<div class="container">

    <h2>Lista de Apartamentos</h2>
    <table id="tablaApartamentos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Dirección</th>
                <th>Persona Encargada</th>
                <th>Fecha de Inicio</th>
                <th>Fecha de Fin</th>
                <th>Estado</th>
                <th>Accion</th>
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
                echo "<td style='text-align:center;'> 
            <a href='editarApartamento.php?id=" . $row['id_apartamento'] . "'><input type='submit' name='Submit'  class='btn btn-warning' value='Editar'></a>
            <input type='submit' name='Submit' class='btn btn-danger' value='Eliminar' onclick=\"eliminarApartamento(" . $row['id_apartamento'] . ")\">
          </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Agrega el archivo JavaScript de DataTables y su dependencia jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
    // Inicializa la tabla DataTables
    $(document).ready(function () {
        $('#tablaApartamentos').DataTable();
    });
</script>
<script src="js/eliminar.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<?php
require_once 'includes/inicio.php';
?>