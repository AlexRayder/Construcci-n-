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
                        header('Location: index.php');
                        exit();
                    case 'Ingeniero civil':
                        header('Location: index.php');
                        exit();
                    case 'Supervisor':
                        header('Location: index.php');
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>


    <?php if (!empty($mensaje)): ?>
        <p id="error">
            <?= $mensaje ?>
        </p>
    <?php endif; ?>
    <div class="container">
        <h1>Login</h1>
        <form id="login" action="login.php" method="POST">
            <div class="form-floating mb-3">
                <input type="number" name="documento" class="form-control" id="floatingInput"
                    placeholder="name@example.com">
                <label for="form-control" id="id_email">Documento</label>
            </div>
            <div class="form-floating">
                <input type="password" name="password" class="form-control" id="floatingPassword"
                    placeholder="Password">
                <label for="floatingPassword">Contraseña</label>
            </div>
            <input type="submit" id="enviar" value="Entrar!">
        </form>

        <?php require_once 'includes/footer.php' ?>
    </div>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>

</html>