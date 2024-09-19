<?php
// Ficha.php - Clase para manejar las operaciones de la ficha

class Ficha {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

         public function agregarFicha($numeroFicha, $numeroArchivo, $estanteria, $idUsuario) {
            $fechaCreacion = date("Y-m-d H:i:s");
            $sqlInsertarFicha = "INSERT INTO fichas (numero, numero_archivo, estanteria, fecha_creacion, usuario_ID) 
                                 VALUES ('$numeroFicha', '$numeroArchivo', '$estanteria', '$fechaCreacion', '$idUsuario')";

            if ($this->conn->query($sqlInsertarFicha) !== TRUE) {
                echo "Error al agregar la ficha: " . $this->conn->error;
            }
        }


    public function eliminarFicha($idFicha) {
        $sqlEliminarFicha = "DELETE FROM fichas WHERE id = '$idFicha'";

        if ($this->conn->query($sqlEliminarFicha) !== TRUE) {
            echo "Error al eliminar la ficha: " . $this->conn->error;
        }
    }

    public function actualizarFicha($idFicha, $numeroFicha) {
        $sqlActualizarFicha = "UPDATE fichas SET numero = '$numeroFicha' WHERE id = '$idFicha'";

        if ($this->conn->query($sqlActualizarFicha) !== TRUE) {
            echo "Error al actualizar la ficha: " . $this->conn->error;
        }
    }

    public function obtenerFichas() {
        $sqlObtenerFichas = "SELECT fichas.*, usuario.username 
                             FROM fichas
                             LEFT JOIN usuario ON fichas.usuario_ID = usuario.id";

        $resultObtenerFichas = $this->conn->query($sqlObtenerFichas);

        if ($resultObtenerFichas !== FALSE) {
            $fichas = ($resultObtenerFichas->num_rows > 0) ? $resultObtenerFichas->fetch_all(MYSQLI_ASSOC) : [];
        } else {
            echo "Error en la consulta: " . $this->conn->error;
            $fichas = [];
        }

        return $fichas;
    }


    public function mostrarFichaEnGrid($fichaItem) {
    echo "<div class='grid-item'>";
    echo "Número de Ficha: " . $fichaItem['numero'] . "<br>";
    echo "Número de Archivo: " . $fichaItem['numero_archivo'] . "<br>";
    echo "Estantería: " . $fichaItem['estanteria'] . "<br>";
    echo "Nombre del Usuario: " . $fichaItem['username'] . "<br>";
    // Agregar más detalles si es necesario
    echo "</div>";
}
}
?>
