<?php
// buscador.php - Archivo que contendrá las funciones de búsqueda

require_once 'Ficha.php';

$ficha = new Ficha($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'buscar_por_numero') {
    $numeroBusqueda = $conn->real_escape_string($_POST['numero_busqueda']);

    $resultBuscarPorNumero = $ficha->buscarPorNumero($numeroBusqueda);

    if ($resultBuscarPorNumero !== FALSE) {
        $fichas = ($resultBuscarPorNumero->num_rows > 0) ? $resultBuscarPorNumero->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        echo "Error en la consulta: " . $conn->error;
        $fichas = [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'buscar_por_fecha') {
    $fechaBusqueda = $conn->real_escape_string($_POST['fecha_busqueda']);

    $resultBuscarPorFecha = $ficha->buscarPorFecha($fechaBusqueda);

    if ($resultBuscarPorFecha !== FALSE) {
        $fichas = ($resultBuscarPorFecha->num_rows > 0) ? $resultBuscarPorFecha->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        echo "Error en la consulta: " . $conn->error;
        $fichas = [];
    }
}
?>
