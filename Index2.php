<?php
session_start();
include('conexion.php');
include('Ficha.php');

// Instanciar la clase Ficha
$ficha = new Ficha($conn);

// Procesar el formulario de agregar ficha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_ficha'])) {
    $numero_ficha = $_POST['numero_ficha'];
    $numero_archivo = $_POST['numero_archivo'];
    $estanteria = $_POST['estanteria'];
    $usuario_id = $_SESSION['id_usuario'];

    // Verificar que el formato de estantería sea "XX-XX-XX"
    if (!preg_match('/^\d{2}-\d{2}-\d{2}$/', $estanteria)) {
        echo "<p style='color: red;'>Formato de estantería incorrecto. Debe ser XX-XX-XX.</p>";
    } else {
        $ficha->agregarFicha($numero_ficha, $numero_archivo, $estanteria, $usuario_id);
    }
}

// Procesar el formulario de búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_ficha'])) {
    $fecha_buscar = $_POST['buscar_fecha'];
    $numero_ficha_buscar = $_POST['buscar_numero_ficha'];

    // Llamar a la función listarFichas con los parámetros de búsqueda
    $resultadosBusqueda = $ficha->obtenerFichas($fecha_buscar, $numero_ficha_buscar);
}

// Buscar fichas por número si se recibe una solicitud POST de búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'buscar_por_numero') {
    // Obtener el número de ficha ingresado en el formulario
    $numeroBusqueda = $conn->real_escape_string($_POST['numero_busqueda']);

    // Query para buscar fichas por número
    $sql = "SELECT fichas.*, usuario.username 
            FROM fichas
            LEFT JOIN usuario ON fichas.usuario_ID = usuario.id
            WHERE fichas.numero = '$numeroBusqueda'";

    // Ejecutar la consulta y manejar errores
    $result = $conn->query($sql);
    if ($result !== FALSE) {
        $fichas = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        echo "Error en la consulta: " . $conn->error;
        $fichas = [];
    }
}

// Buscar fichas por fecha si se recibe una solicitud POST de búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'buscar_por_fecha') {
    // Obtener la fecha de búsqueda ingresada en el formulario
    $fechaBusqueda = $conn->real_escape_string($_POST['fecha_busqueda']);

    // Query para buscar fichas por fecha
    $sql = "SELECT fichas.*, usuario.username 
            FROM fichas
            LEFT JOIN usuario ON fichas.usuario_ID = usuario.idate(format)
            WHERE DATE(fichas.fecha_creacion) = '$fechaBusqueda'";

    // Ejecutar la consulta y manejar errores
    $result = $conn->query($sql);
    if ($result !== FALSE) {
        $fichas = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        echo "Error en la consulta: " . $conn->error;
        $fichas = [];
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicación de Fichas</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #333;
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
        }

        .form-container {
            margin-bottom: 20px;
        }

        .separator {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .grid-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        h2 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<!-- Encabezado con enlaces de navegación -->
<nav>
    <a href="index.php">Inicio</a>
    <a href="crear_usuario.php">Crear Usuario</a>
    <a href="login.php">Iniciar Sesión</a>
    <!-- Agrega más enlaces según tus necesidades -->
</nav>

<div class="container">
    <!-- Formulario de ingreso y búsqueda de fichas -->
    <form method="post" action="">
        <!-- Campos para agregar ficha -->
        <label for="numero_ficha">Número de Ficha:</label>
        <input type="text" name="numero_ficha" required><br>

        <label for="numero_archivo">Número de Archivo:</label>
        <input type="text" name="numero_archivo" required><br>

        <label for="estanteria">Estantería (Formato XX-XX-XX):</label>
        <input type="text" name="estanteria" pattern="\d{2}-\d{2}-\d{2}" required title="Formato válido: XX-XX-XX"><br>

        <input type="submit" name="agregar_ficha" value="Agregar Ficha">
    </form>

    <!-- Formulario de búsqueda de fichas -->
    <div class="form-container">
        <form action="" method="post">
            <label for="numero_busqueda">Buscar por Número de Ficha:</label>
            <input type="text" name="numero_busqueda" id="numero_busqueda" required>
            <input type="hidden" name="accion" value="buscar_por_numero">
            <button type="submit">Buscar por Número de Ficha</button>
        </form>
    </div>

    <div class="form-container">
        <form action="" method="post">
            <label for="fecha_busqueda">Fecha a Buscar:</label>
            <input type="date" name="fecha_busqueda" id="fecha_busqueda" required>
            <input type="hidden" name="accion" value="buscar_por_fecha">
            <button type="submit">Buscar por Fecha</button>
        </form>
    </div>

    <div class="separator"></div>

    <!-- Mostrar la lista de fichas -->
    <?php
    if (isset($resultadosBusqueda)) {
        echo "<h2>Resultados de la Búsqueda:</h2>";
        foreach ($resultadosBusqueda as $resultado) {
            $ficha->mostrarFichaEnGrid($resultado);
        }
    } else {
        echo "<h2>Listado de Fichas:</h2>";
        $todasLasFichas = $ficha->obtenerFichas();
        foreach ($todasLasFichas as $fichaItem) {
            $ficha->mostrarFichaEnGrid($fichaItem);
        }
    }
    ?>
</div>

</body>
</html>
