<?php
// crud.php - Archivo que contendrá las operaciones CRUD

require_once 'Ficha.php';

$ficha = new Ficha($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar_ficha') {
    $ficha->agregarFicha(
        $conn->real_escape_string($_POST['numero_ficha']),
        $conn->real_escape_string($_POST['numero_archivo']),
        $conn->real_escape_string($_POST['estanteria']),
        $idUsuario
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $ficha->eliminarFicha($conn->real_escape_string($_GET['eliminar']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_ficha') {
    $ficha->actualizarFicha(
        $conn->real_escape_string($_POST['id_ficha']),
        $conn->real_escape_string($_POST['numero_ficha'])
    );
}
?>
    