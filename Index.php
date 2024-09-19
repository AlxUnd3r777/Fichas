<?php
session_start();
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fichas2";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);
//require __DIR__ . '/vendor/autoload.php';

//use PhpOffice\PhpWord\PhpWord;
//use PhpOffice\PhpWord\IOFactory as WordIOFactory;
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Verificar la conexión a la base de datos
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres para la conexión
$conn->set_charset("utf8mb4");


// Cierre de sesión si se recibe una solicitud POST de cierre de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: log.php");
    exit();
}



$idUsuario = $_SESSION['id_usuario'] ?? null;

if (!$idUsuario) {
    echo "Error: ID de usuario no disponible. que mal  XD !! lo mejoraremos";
    exit();
}

// Obtener el nombre del usuario
$sqlVerificarUsuario = "SELECT id, username FROM usuario WHERE id = '$idUsuario'";
$resultadoVerificarUsuario = $conn->query($sqlVerificarUsuario);

if ($resultadoVerificarUsuario === FALSE) {
    echo "Error en la consulta SQL: " . $conn->error;
    exit();
}

if ($resultadoVerificarUsuario->num_rows === 0) {
    echo "Error: El usuario no existe en la tabla usuario";
    exit();
}

$usuario = $resultadoVerificarUsuario->fetch_assoc();
$nombreUsuario = $usuario['username'];

// Query para insertar una nueva ficha en la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar_ficha') {
    $numeroFicha = $conn->real_escape_string($_POST['numero_ficha']);
    $numeroArchivo = $conn->real_escape_string($_POST['numero_archivo']);
    $estanteria = $conn->real_escape_string($_POST['estanteria']);
    $fechaCreacion = date("Y-m-d H:i:s");

    $sqlInsertarFicha = "INSERT INTO fichas (numero, numero_archivo, estanteria, fecha_creacion, id_usuario) 
                         VALUES ('$numeroFicha', '$numeroArchivo', '$estanteria', '$fechaCreacion', '$idUsuario')";

    // Ejecutar la consulta y manejar errores
    if ($conn->query($sqlInsertarFicha) !== TRUE) {
        echo "Error al agregar la ficha: " . $conn->error;
    }
}

// Eliminar una ficha si se recibe una solicitud GET de eliminar
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $idFichaEliminar = $conn->real_escape_string($_GET['eliminar']);
    $sqlEliminarFicha = "DELETE FROM fichas WHERE id = '$idFichaEliminar'";

    // Ejecutar la consulta y manejar errores
    if ($conn->query($sqlEliminarFicha) !== TRUE) {
        echo "Error al eliminar la ficha: " . $conn->error;
    }
}

// Actualizar una ficha si se recibe una solicitud POST de actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_ficha') {
    $idFichaActualizar = $conn->real_escape_string($_POST['id_ficha']);
    $numeroFichaActualizar = $conn->real_escape_string($_POST['numero_ficha']);

    // Query para actualizar el número de una ficha en la base de datos
    $sqlActualizarFicha = "UPDATE fichas SET numero = '$numeroFichaActualizar' WHERE id = '$idFichaActualizar'";

    // Ejecutar la consulta y manejar errores
    if ($conn->query($sqlActualizarFicha) !== TRUE) {
        echo "Error al actualizar la ficha: " . $conn->error;
    }
}

// Obtener fichas de la base de datos junto con los nombres de usuarios
$sqlObtenerFichas = "SELECT fichas.*, usuario.username 
                     FROM fichas
                     LEFT JOIN usuario ON fichas.usuario_ID = usuario.id";

// Ejecutar la consulta y manejar errores
$resultObtenerFichas = $conn->query($sqlObtenerFichas);
if ($resultObtenerFichas !== FALSE) {
    $fichas = ($resultObtenerFichas->num_rows > 0) ? $resultObtenerFichas->fetch_all(MYSQLI_ASSOC) : [];
} else {
    echo "Error en la consulta: " . $conn->error;
    $fichas = [];
}

// Buscar fichas por número si se recibe una solicitud POST de búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'buscar_por_numero') {
    // Obtener el número de ficha ingresado en el formulario
    $numeroBusqueda = $conn->real_escape_string($_POST['numero_busqueda']);

    // Query para buscar fichas por número
    $sqlBuscarPorNumero = "SELECT fichas.*, usuario.username 
                           FROM fichas
                           LEFT JOIN usuario ON fichas.id_usuario = usuario.id
                           WHERE fichas.numero = '$numeroBusqueda'";

    // Ejecutar la consulta y manejar errores
    $resultBuscarPorNumero = $conn->query($sqlBuscarPorNumero);
    if ($resultBuscarPorNumero !== FALSE) {
        $fichas = ($resultBuscarPorNumero->num_rows > 0) ? $resultBuscarPorNumero->fetch_all(MYSQLI_ASSOC) : [];
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
    $sqlBuscarPorFecha = "SELECT fichas.*, usuario.username 
                          FROM fichas
                          LEFT JOIN usuario ON fichas.id_usuario = usuario.id
                          WHERE DATE(fichas.fecha_creacion) = '$fechaBusqueda'";

    // Ejecutar la consulta y manejar errores
    $resultBuscarPorFecha = $conn->query($sqlBuscarPorFecha);
    if ($resultBuscarPorFecha !== FALSE) {
        $fichas = ($resultBuscarPorFecha->num_rows > 0) ? $resultBuscarPorFecha->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        echo "Error en la consulta: " . $conn->error;
        $fichas = [];
    }
}

// Exportar fichas a Excel o Word si se recibe una solicitud POST de exportación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo'])) {
    $tipoExportacion = $_POST['tipo'];

    // Verificar el tipo de exportación y redirigir a exportar.php con los datos de las fichas
    if ($tipoExportacion === 'excel' || $tipoExportacion === 'word') {
        echo '<form id="exportForm" method="post" action="exportar.php">';
        echo '<input type="hidden" name="tipo" value="' . $tipoExportacion . '">';
        echo '<input type="hidden" name="fichas" value=\'' . json_encode($fichas) . '\'>';
        echo '</form>';

        echo '<script>';
        echo 'document.getElementById("exportForm").submit();';
        echo '</script>';
    } else {
        echo "Tipo de exportación no válido.";
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
        <title>Registro de Fichas - Sección Archivos</title>
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

            .navbar {
                overflow: hidden;
                background-color: #333;
                font-size: 14px;
            }

            .navbar a {
                float: left;
                display: block;
                color: #fff;
                text-align: center;
                padding: 8px 10px;
                text-decoration: none;
            }

            .navbar a:hover {
                background-color: #ddd;
                color: black;
            }

            h2 {
                margin-bottom: 20px;
                opacity: 0;
                transform: translateY(-20px);
                animation: fadeInUp 1s forwards;
            }

            @keyframes fadeInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .form-container {
                width: 30%;
                margin-bottom: 20px;
                display: inline-block;
                vertical-align: top;
            }

            form {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background-color: #222;
                border: 1px solid #e44d26;
                border-radius: 5px;
                margin: 10px;
            }

            form label {
                margin-bottom: 5px;
            }

            form input,
            form select {
                margin: 5px;
                padding: 10px;
                border: 1px solid #e44d26;
                background-color: #333;
                color: #fff;
                font-family: 'Orbitron', sans-serif;
                border-radius: 3px;
            }

            form select {
                width: 100%;
            }

            form button {
                margin-top: 10px;
                background-color: #e44d26;
                color: #fff;
                border: 1px solid #e44d26;
                padding: 10px;
                cursor: pointer;
            }

            .export-button {
                background-color: #e44d86;
                color: #fff;
                border: 1px solid #e44d26;
                padding: 10px;
                cursor: pointer;
                margin-top: 20px;
            }

            .separator {
                height: 10px;
                background-color: #111;
            }

            table {
                width: 100%;
                margin-top: 20px;
                border-collapse: collapse;
                overflow: hidden;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                margin-bottom: 20px;
            }

            th,
            td {
                border: 1px solid #444;
                background-color: #333;
                color: #fff;
                font-family: 'Orbitron', sans-serif;
                transition: background-color 0.3s;
                padding: 10px;
            }

            th:hover,
            td:hover {
                background-color: #555;
            }

            th a {
                color: #fff;
                text-decoration: none;
                display: inline-block;
                transition: color 0.3s;
            }

            th a:hover {
                color: #e44d26;
            }

            .acciones-container {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 5px;
            }

            .acciones-container a,
            .acciones-container button {
                background-color: #e44d26;
                color: #fff;
                border: 1px solid #e44d26;
                padding: 8px 12px;
                text-decoration: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .acciones-container a:hover,
            .acciones-container button:hover {
                background-color: #ff6655;
                border-color: #ff6655;
            }
        </style>

    </head>

        <body>
            <div class="navbar">
                <a href="#">Inicio</a>
                <a href="#">Ficha</a>
                <a href="#" onclick="document.getElementById('logoutForm').submit();">Cerrar Sesión</a>
                <a href="#" style="color: #e44d26;">Bienvenido, <?php echo $_SESSION['nombre_usuario']; ?></a>
                <!-- Formulario de cierre de sesión -->
                <form id="logoutForm" action="" method="post" style="display: none;">
                    <input type="hidden" name="logout">
                </form>
            </div>

            <?php
            // Cálculo del número de ingresos del día
            $num_ingresos_dia = 0; // Establecer un valor predeterminado

            // Verificar si se ha enviado el formulario de cierre de sesión
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
                // Destruir la sesión y redirigir a la página de inicio de sesión
                session_destroy();
                header("Location: log.php");
                exit();
            }

            // Lógica de cálculo del número de ingresos del día
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
                if ($_POST['accion'] === 'agregar_ficha') {
                    // Incrementar el contador de ingresos del día al agregar una ficha
                    $num_ingresos_dia++;
                }
            }
            ?>
            </div>

            <h2>Registro de Fichas - Sección Archivos</h2>

            <!-- Formulario para agregar una nueva ficha -->
            <div class="form-container">
                <form action="" method="post">
                    <label for="numero_ficha">Número de Ficha:</label>
                    <input type="text" name="numero_ficha" id="numero_ficha" required>

                    <label for="numero_archivo">Número de Archivo:</label>
                    <select name="numero_archivo" id="numero_archivo" required>
                        <?php for ($i = 1; $i <= 7; $i++) : ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>

                    <label for="estanteria">Estantería:</label>
                    <input type="text" name="estanteria" id="estanteria" required>

                    <input type="hidden" name="accion" value="agregar_ficha">
                    <button type="submit">Agregar Ficha</button>
                </form>
            </div>

            <!-- Línea separadora entre ingresos y búsquedas -->
            <div class="separator"></div>

            <h2>Busqueda de Fichas</h2>

            <!-- Formulario para buscar por número de ficha -->
            <div class="form-container">
                <form action="" method="post">
                    <label for="numero_busqueda">Buscar por Número de Ficha:</label>
                    <input type="text" name="numero_busqueda" id="numero_busqueda" required>
                    <input type="hidden" name="accion" value="buscar_por_numero"> <!-- Asegúrate de tener este campo oculto -->
                    <button type="submit">Buscar por Número de Ficha</button>
                </form>
            </div>

            <!-- Formulario para buscar por fecha específica -->
            <div class="form-container">
                <form action="" method="post">
                    <label for="fecha_busqueda">Fecha a Buscar:</
                    <label for="fecha_busqueda">Fecha a Buscar:</label>
                    <input type="date" name="fecha_busqueda" id="fecha_busqueda" required>
                    <input type="hidden" name="accion" value="buscar_por_fecha"> <!-- Asegúrate de tener este campo oculto -->
                    <button type="submit">Buscar por Fecha</button>
                </form>
            </div>

            <!-- Línea separadora entre búsquedas y resultados -->
            <div class="separator"></div>

            <h2>Resultados de Búsqueda</h2>

            <!-- Tabla para mostrar los resultados de la búsqueda -->
    
            <table>
            <thead>
                <tr>
                    <th>Número de Ficha</th>
                    <th>Número de Archivo</th>
                    <th>Estantería</th>
                    <th>Fecha de Creación</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
              <!-- ... tu tabla HTML ... -->
<tbody>
    <?php foreach ($fichas as $ficha) : ?>
        <tr>
            <td><?= $ficha['numero'] ?></td>
            <td><?= $ficha['numero_archivo'] ?></td>
            <td><?= $ficha['estanteria'] ?></td>
            <td><?= $ficha['fecha_creacion'] ?></td>
            <td><?= $ficha['username'] ?></td> <!-- Asegúrate de ajustar el nombre del índice según lo que hayas obtenido -->
            <td class="acciones-container">
                <a href="?eliminar=<?= $ficha['id'] ?>" onclick="return confirm('¿Seguro que desea eliminar esta ficha?')" class="btn-eliminar">Eliminar</a>
                <button onclick="editarFicha(<?= $ficha['id'] ?>, '<?= $ficha['numero'] ?>')" class="btn-editar">Editar</button>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>

            </tbody>
        </table>

        <script>
            // Función JavaScript para exportar resultados
            function exportar(tipo) {
                var form = document.createElement('form');
                form.method = 'post';
                form.action = 'exportar.php';

                var inputTipo = document.createElement('input');
                inputTipo.type = 'hidden';
                inputTipo.name = 'tipo';
                inputTipo.value = tipo;

                var inputFichas = document.createElement('input');
                inputFichas.type = 'hidden';
                inputFichas.name = 'fichas';
                inputFichas.value = JSON.stringify(<?= json_encode($fichas) ?>);

                form.appendChild(inputTipo);
                form.appendChild(inputFichas);

                document.body.appendChild(form);
                form.submit();
            }

            function editarFicha(id, numero) {
                var nuevoNumero = prompt('Editar número de ficha:', numero);
                
                if (nuevoNumero !== null) {
                    var form = document.createElement('form');
                    form.method = 'post';
                    form.action = '';
                    
                    var inputAccion = document.createElement('input');
                    inputAccion.type = 'hidden';
                    inputAccion.name = 'accion';
                    inputAccion.value = 'actualizar_ficha';
                    
                    var inputIdFicha = document.createElement('input');
                    inputIdFicha.type = 'hidden';
                    inputIdFicha.name = 'id_ficha';
                    inputIdFicha.value = id;
                    
                    var inputNumeroFicha = document.createElement('input');
                    inputNumeroFicha.type = 'hidden';
                    inputNumeroFicha.name = 'numero_ficha';
                    inputNumeroFicha.value = nuevoNumero;
                    
                    form.appendChild(inputAccion);
                    form.appendChild(inputIdFicha);
                    form.appendChild(inputNumeroFicha);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
    </body>
    </html>