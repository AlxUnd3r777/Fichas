<?php
// exportar.php - Archivo para exportar fichas a Excel o Word

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo']) && isset($_POST['fichas'])) {
    $tipoExportacion = $_POST['tipo'];

    if ($tipoExportacion === 'excel' || $tipoExportacion === 'word') {
        echo '<form id="exportForm" method="post" action="exportar.php">';
        echo '<input type="hidden" name="tipo" value="' . $tipoExportacion . '">';
        echo '<input type="hidden" name="fichas" value=\'' . $_POST['fichas'] . '\'>';
        echo '</form>';

        echo '<script>';
        echo 'document.getElementById("exportForm").submit();';
        echo '</script>';
    } else {
        echo "Tipo de exportación no válido.";
    }
}
?>
