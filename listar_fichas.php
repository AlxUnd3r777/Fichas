<?php
function listarFichas($conn, $fechaBusqueda = null, $numeroFichaBusqueda = null) {
    // Verificar si la sesión está activa antes de iniciarla
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Asumo que la tabla de usuarios tiene una columna llamada 'username'
    $sql = "SELECT f.id, f.numero, f.fecha_creacion, f.numero_archivo, f.estanteria, u.username as nombre_usuario
            FROM fichas f
            JOIN usuario u ON f.usuario_ID = u.id";

    // Agregar condiciones de búsqueda si se proporcionan valores
    if ($fechaBusqueda !== null) {
        $sql .= " WHERE DATE(f.fecha_creacion) = '$fechaBusqueda'";
    }

    if ($numeroFichaBusqueda !== null) {
        // Asumo que el número de ficha es una cadena
        $sql .= " AND f.numero = '$numeroFichaBusqueda'";
    }

    $result = $conn->query($sql);

    if ($result) {
        echo "<h2>Listado de Fichas:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Número de Ficha</th><th>Fecha de Creación</th><th>Número de Archivo</th><th>Estantería</th><th>Usuario</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['numero'] . "</td>";
            echo "<td>" . $row['fecha_creacion'] . "</td>";
            echo "<td>" . $row['numero_archivo'] . "</td>";

            // Convertir el número de estantería al formato deseado (23-23-23)
            $estanteria = $row['estanteria'];
            $formatted_estanteria = substr($estanteria, 0, 2) . '-' . substr($estanteria, 2, 2) . '-' . substr($estanteria, 4);
            echo "<td>" . $formatted_estanteria . "</td>";

            // Mostrar el nombre del usuario
            echo "<td>" . $row['nombre_usuario'] . "</td>";

            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No hay fichas disponibles.";
    }
}
?>
