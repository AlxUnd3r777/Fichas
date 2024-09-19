<?php
// log.php - Página de inicio de sesión

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fichas2";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión a la base de datos
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres para la conexión
$conn->set_charset("utf8mb4");
session_start();

// Verificar si ya hay una sesión iniciada
if (isset($_SESSION['id_usuario'])) {
    header("Location: fichas.php");
    exit();
}

// Manejar el inicio de sesión si se recibe una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Consulta para verificar las credenciales del usuario
    $sqlVerificarUsuario = "SELECT id, username FROM usuarios WHERE username = '$username' AND password = '$password'";
    $resultadoVerificarUsuario = $conn->query($sqlVerificarUsuario);

    if ($resultadoVerificarUsuario === FALSE) {
        echo "Error en la consulta SQL: " . $conn->error;
        exit();
    }

    // Verificar si las credenciales son válidas
    if ($resultadoVerificarUsuario->num_rows > 0) {
        // Iniciar sesión y redirigir a la página principal
        $usuario = $resultadoVerificarUsuario->fetch_assoc();
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nombre_usuario'] = $usuario['username'];
        header("Location: fichas.php");
        exit();
    } else {
        echo "Credenciales inválidas.";
    }
}

// Cerrar la conexión a la base de datos
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
        body {
            margin: 0;
            padding: 0;
            font-family: 'Orbitron', sans-serif;
            background-color: #111;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .login-container {
            width: 30%;
            margin: auto;
            margin-top: 100px;
        }

        form {
            background-color: #222;
            border: 1px solid #e44d26;
            border-radius: 5px;
            padding: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 18px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #e44d26;
            background-color: #333;
            color: #fff;
            font-family: 'Orbitron', sans-serif;
            border-radius: 3px;
        }

        button {
            background-color: #e44d26;
            color: #fff;
            border: 1px solid #e44d26;
            padding: 10px;
            cursor: pointer;
            font-size: 18px;
            border-radius: 5px;
        }
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
    </div>
</body>
</html>
