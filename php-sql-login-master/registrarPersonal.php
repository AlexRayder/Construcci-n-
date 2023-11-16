<?php

require_once 'includes/conexion.php';

$mensaje = "";
$guardar = false; // Declarar la variable $guardar

// Verificar si se ha enviado el formulario y si hay datos completos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['documento']) && !empty($_POST['nombre']) && !empty($_POST['apellido']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $documento = $_POST['documento'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Cifrado de contraseña utilizando password_hash
        $password_segura = password_hash($password, PASSWORD_BCRYPT);

        // Consulta para verificar si ya existe el documento o el correo
        $consulta_existencia = "SELECT * FROM usuarios WHERE documento = ? OR email = ?";
        $stmt_existencia = mysqli_prepare($db, $consulta_existencia);
        mysqli_stmt_bind_param($stmt_existencia, "ss", $documento, $email);
        mysqli_stmt_execute($stmt_existencia);
        mysqli_stmt_store_result($stmt_existencia);

        if (mysqli_stmt_num_rows($stmt_existencia) > 0) {
       
            $mensaje = "Documento o correo electrónico ya existen. No se puede registrar.";

        } else {
            // Consulta preparada para evitar la inyección SQL
            // Después de obtener el valor de $password_segura
            $rol_id = $_POST['rol'];

            $sql = "INSERT INTO usuarios (documento, nombre, apellido, email, contrasena, id_rol) VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($db, $sql);

            if ($stmt) {
                // Vinculamos los parámetros
                mysqli_stmt_bind_param($stmt, "sssssi", $documento, $nombre, $apellido, $email, $password_segura, $rol_id);

                // Ejecutamos la consulta
                $guardar = mysqli_stmt_execute($stmt);

                // Cerramos la declaración
                mysqli_stmt_close($stmt);

                if ($guardar) {
                    // Registro exitoso, reiniciar variables y mostrar mensaje de confirmación
                    $mensaje = "Registro exitoso. Todos los campos se han guardado.";
                    $_POST = array(); // Reiniciar los valores del formulario
                }
            } else {
                // Loguear el error o redirigir a una página de error
                $mensaje = "Ha ocurrido un error al preparar la consulta: " . mysqli_error($db);
            }
        }

        // Cerramos la consulta de existencia
        mysqli_stmt_close($stmt_existencia);
    } else {
        $mensaje = "Todos los campos son obligatorios. Por favor, completa todos los campos.";
    }
}
?>

<?php require_once 'includes/encabezado.php' ?>

<!-- SweetAlert2 CSS desde CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS desde CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<div class="container">
    <div style="text-align: center;">
        <h1>Registro Personal</h1>
    </div>

    <link rel="stylesheet" href="css/styles.css">
    <form id="registro" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div id="frmAgregar">
            <!-- Ejemplo para el campo de documento -->
            <div class="form-floating mb-3">
                <input type="number" class="form-control" name="documento" id="floatingInput" placeholder="1234567891"
                    value="<?php echo isset($_POST['documento']) ? htmlspecialchars($_POST['documento']) : ''; ?>">
                <label for="floatingInput">Documento:</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="nombre" id="floatingInput" placeholder="pepito"
                    value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                <label for="floatingInput">Nombre:</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="apellido" id="floatingInput" placeholder="1234567891"
                    value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>">
                <label for="floatingInput">Apellido:</label>
            </div>

            <div class="form-floating mb-3">
                <input type="email" class="form-control" name="email" id="floatingInput" placeholder="name@example.com"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <label for="floatingInput">Correo:</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
                <label for="floatingPassword">Password:</label>
            </div>

            <div class="form-floating mb-3">
                <select class="form-select" id="floatingSelect" name="rol" aria-label="Floating label select example">
                    <option value="1" <?php echo (isset($_POST['rol']) && $_POST['rol'] == '1') ? 'selected' : ''; ?>>
                        Administrador</option>
                    <option value="2" <?php echo (isset($_POST['rol']) && $_POST['rol'] == '2') ? 'selected' : ''; ?>>
                        Ingeniero Civil</option>
                    <option value="3" <?php echo (isset($_POST['rol']) && $_POST['rol'] == '3') ? 'selected' : ''; ?>>
                        Supervisor</option>
                </select>
                <label for="floatingSelect">Selecciona un Rol:</label>
            </div>

            <div>
                <input type="submit" id="singup" value="Registrarse" class="btn btn-success">
            </div>
        </div>
    </form>

    <?php if (!empty($mensaje)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    position: "center",
                    icon: "<?php echo ($guardar) ? 'success' : 'error'; ?>",
                    title: "<?php echo $mensaje; ?>",
                    showConfirmButton: false,
                    timer: 4000
                });

                <?php if (!$guardar): ?>
                    // Si hay un error, mostrar el mensaje de error
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "<?php echo $mensaje; ?>",
                    });
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>
</div>

<?php require_once 'includes/inicio.php' ?>
