<?php
require_once 'includes/conexion.php';

$mensaje = "";

if (!empty($_POST['nombre']) && !empty($_POST['email']) && !empty($_POST['password'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cifrado de contraseña utilizando password_hash
    $password_segura = password_hash($password, PASSWORD_BCRYPT);

    // Consulta preparada para evitar la inyección SQL
    // Después de obtener el valor de $password_segura
    $rol_id = $_POST['rol'];

    $sql = "INSERT INTO usuarios (nombre, email, contrasena, id_rol) VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($db, $sql);

    if ($stmt) {
        // Vinculamos los parámetros
        mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $password_segura, $rol_id);

        // Ejecutamos la consulta
        $guardar = mysqli_stmt_execute($stmt);

        // Verificamos si la consulta se ejecutó correctamente
        if ($guardar) {
            $mensaje = "Registrado con éxito";
        } else {
            // Loguear el error o redirigir a una página de error
            $mensaje = "Ha ocurrido un error al registrarse.";
        }

        // Cerramos la declaración
        mysqli_stmt_close($stmt);
    } else {
        // Loguear el error o redirigir a una página de error
        $mensaje = "Ha ocurrido un error al preparar la consulta.";
    }

}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de registro</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>


    <?php if (!empty($mensaje)): ?>
        <p id="s-exito">
            <?= $mensaje ?>
        </p>
    <?php endif; ?>

    <h1>SingUp</h1>
    <span>or <a href="login.php">Login</a></span>

    <form id="registro" action="singup.php" method="POST">
        <label for="nombre" id="login-nombre">Nombre</label>
        <input type="text" name="nombre" id="i-nombre" placeholder="Introduce un nombre">
        <label for="email" id="login-email">Email</label>
        <input type="email" name="email" id="i-email" placeholder="Introduce tu email">

        <label for="password" id="login-pass">Contraseña</label>
        <input type="password" name="password" id="i-pass" placeholder="Introduce tu contraseña">

        <label for="rol">Rol</label>
        <select name="rol" id="rol">
            <option value="1">Administrador</option>
            <option value="2">Ingeniero Civil</option>
            <option value="3">Supervisor</option>
        </select>


        <input type="submit" id="singup" value="Registrarse">

    </form>

    <?php require_once 'includes/footer.php' ?>
</body>

</html>