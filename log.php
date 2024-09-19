<?php
// log.php - Página de inicio de sesión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fichas2";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
session_start();

if (isset($_SESSION['id_usuario'])) {
    header("Location: Index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sqlVerificarUsuario = "SELECT id, username, password FROM usuario WHERE username = ?";
    $stmt = $conn->prepare($sqlVerificarUsuario);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['id_usuario'] = $id;
            $_SESSION['nombre_usuario'] = $username;
            $stmt->close();  // Cerrar el statement antes de la redirección
            header("Location: Index.php");
            exit();
        } else {
            echo "Credenciales inválidas.";
        }
    } else {
        echo "Credenciales inválidas.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap">
    <style>
        /* Puedes copiar los estilos aquí o enlazar a un archivo de estilos externo */
    </style>
</head>
<body>
    <div class="login-container">
        <form action="" method="post">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Iniciar Sesión</button>
        </form>

        <!-- Enlace al formulario de creación de usuario -->
        <a href="crear_usuario.php">Crear Nuevo Usuario</a>
    </div>
</body>
</html>
