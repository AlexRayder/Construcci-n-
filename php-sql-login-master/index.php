<?php
session_start();
require_once 'includes/conexion.php';

// Verificar si existe la sesión con el id_usuario
if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];

    // Consulta SQL para obtener el usuario por su id_usuario
    $sql = "SELECT id, nombre, apellido, email FROM usuarios WHERE id = '$id_usuario'";
    $result = mysqli_query($db, $sql);

    // Verificar si la consulta fue exitosa
    if ($result !== false) {
        // Obtener el resultado de la consulta
        $usuario = mysqli_fetch_assoc($result);

        // Verificar si se encontró un usuario
        if ($usuario !== null) {
            $nombre = $usuario['nombre'];
            $apellido = $usuario['apellido'];
            $email = $usuario['email'];
        } else {
            // Manejar el caso en que no se encontró un usuario
            $nombre = "Nombre no disponible";
            $apellido = "Apellido no disponible";
            $email = "Email no disponible";
        }
    } else {
        // Manejar el error de la consulta
        die("Error en la consulta: " . mysqli_error($db));
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Formulario de registro y acceso</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>


    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>

                        </ul>
                    </li>
                </ul>
                <!-- Mostrar botones de inicio de sesión y registro para usuarios no logeados -->
                <a href="login.php">Iniciar sesión</a>
                <a href="singup.php">Registrarse</a>

            </div>
        </div>
    </nav>
<div><h1>Bienvenido</h1></div>
        <?php require_once 'includes/footer.php' ?>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>

</html>