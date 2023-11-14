<?php
session_start();

require_once 'includes/conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['documento']) && !empty($_POST['password'])) {
        $documento = mysqli_real_escape_string($db, $_POST['documento']);
        $password = $_POST['password'];

        // Modifica la consulta para incluir la información del nombre, apellido y rol
        $sql = "SELECT u.id, u.contrasena, u.nombre, u.apellido, r.descripcion as rol 
                FROM usuarios u
                JOIN rol r ON u.id_rol = r.id
                WHERE u.documento = '$documento'";

        $result = mysqli_query($db, $sql);

        if ($result !== false) {
            $usuario = mysqli_fetch_assoc($result);

            if ($usuario !== null && password_verify($password, $usuario['contrasena'])) {
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['apellido'] = $usuario['apellido'];
                $_SESSION['rol'] = $usuario['rol']; // Guarda el rol en la sesión

                // Redirige según el rol
                switch ($usuario['rol']) {
                    case 'Administrador':
                        header('Location: administrador/inicioAdministrador.php');
                        exit();
                    case 'Ingeniero civil':
                        header('Location: ingeniero-civil/inicioIngeniero.php');
                        exit();
                    case 'Supervisor':
                        header('Location: supervisor/inicioSupervisor.php');
                        exit();
                    // Agrega más roles según sea necesario
                    default:
                        // Redirige a una página por defecto si el rol no está definido
                        header('Location: index.php');
                        exit();
                }
            } else {
                $mensaje = "Lo sentimos, sus datos son incorrectos";
            }
        } else {
            die("Error en la consulta: " . mysqli_error($db));
        }
    } else {
        $mensaje = "Por favor, complete todos los campos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accede con tu cuenta</title>

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>


    <?php if (!empty($mensaje)): ?>
        <p id="error">
            <?= $mensaje ?>
        </p>
    <?php endif; ?>

    <h1>Login</h1>
    <span>or <a href="singup.php">SingUp</a></span>

    <form id="login" action="login.php" method="POST">
        <label for="email" id="id_email">Documento</label>
        <input type="number" name="documento" placeholder="Introduce tu documento">
        <label for="password" id="id_pass">Contraseña</label>
        <input type="password" name="password" placeholder="Introduce tu contraseña">

        <input type="submit" id="enviar" value="Entrar!">
    </form>

    <?php require_once 'includes/footer.php' ?>
</body>

</html>
