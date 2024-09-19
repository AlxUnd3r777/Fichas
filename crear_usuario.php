<?php
// crear_usuario.php - Formulario para crear un nuevo usuario

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fichas2";

$conn = new mysqli($servername, $username, $password, $dbname);

session_start();

if (isset($_SESSION['id_usuario'])) {
    header("Location: Index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    $nuevo_username = $conn->real_escape_string($_POST['nuevo_username']);
    $nueva_password = password_hash($_POST['nueva_password'], PASSWORD_DEFAULT);

    $sqlCrearUsuario = "INSERT INTO usuario (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sqlCrearUsuario);
    $stmt->bind_param("ss", $nuevo_username, $nueva_password);
    $stmt->execute();
    $stmt->close();

    // Después de crear el usuario, redirigir a la página de inicio de sesión
    header("Location: log.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<!-- ... (Resto del código HTML) ... -->
</html>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Usuario</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap">
    <style>
        /* Estilos del formulario de creación de usuario */
        /* ... Puedes copiar los estilos del formulario de inicio de sesión o personalizarlos según tu preferencia */
    </style>
</head>
<body>
    <div class="crear-usuario-container">
        <form action="" method="post">
            <!-- Campos del formulario para crear un nuevo usuario -->
            <label for="nuevo_username">Nuevo Nombre de Usuario:</label>
            <input type="text" name="nuevo_username" required>

            <label for="nueva_password">Nueva Contraseña:</label>
            <input type="password" name="nueva_password" required>

            <button type="submit" name="crear_usuario">Crear Usuario</button>
        </form>
    </div>
</body>
</html>
