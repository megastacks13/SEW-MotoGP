<?php


class Configuracion {
    private $conn;
    private $db_host;
    private $db_user;
    private $db_pass;
    private $db_name;

    public function __construct() {
        $this->db_host = "localhost";
        $this->db_user = "DBUSER2025";
        $this->db_pass = "DBPSWD2025";
        $this->db_name = "UO294024_DB";

        $this->conectar();
    }

    private function conectar() {
        $this->conn = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if ($this->conn->connect_error) {
            throw new Exception("Error de conexión: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8");
    }

    public function reiniciarBaseDatos() {
        $this->conn->query("SET FOREIGN_KEY_CHECKS = 0");

        $tables = $this->conn->query("SHOW TABLES");
        while ($row = $tables->fetch_array()) {
            $table = $row[0];
            $this->conn->query("TRUNCATE TABLE `$table`");
        }

        $this->conn->query("SET FOREIGN_KEY_CHECKS = 1");

        return ["success" => true, "message" => "Base de datos reiniciada correctamente"];
    }


    public function eliminarBaseDatos() {
        $this->conn->query("SET FOREIGN_KEY_CHECKS = 0");

        // Eliminar todas las tablas
        $tables = $this->conn->query("SHOW TABLES");
        while ($row = $tables->fetch_array()) {
            $table = $row[0];
            $this->conn->query("DROP TABLE IF EXISTS `$table`");
        }

        // Eliminar la base de datos
        $this->conn->query("DROP DATABASE IF EXISTS `{$this->db_name}`");

        // Crear nuevamente la base de datos vacía
        $this->conn->query("CREATE DATABASE IF NOT EXISTS `{$this->db_name}`");
        $this->conn->select_db($this->db_name);

        $this->conn->query("SET FOREIGN_KEY_CHECKS = 1");

        return ["success" => true, "message" => "Base de datos eliminada y recreada correctamente"];
    }


    public function exportarCSV($directorio = "./backup") {
        // Crear directorio si no existe
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $export_dir = $directorio . "/export_" . $timestamp;
        mkdir($export_dir, 0777, true);

        $tables = $this->conn->query("SHOW TABLES");
        $exported_tables = [];

        while ($row = $tables->fetch_array()) {
            $table = $row[0];
            $filename = $export_dir . "/" . $table . ".csv";

            // Obtener datos de la tabla
            $result = $this->conn->query("SELECT * FROM `$table`");

            $file = fopen($filename, 'w');

            // Escribir encabezados
            $fields = $result->fetch_fields();
            $headers = [];
            foreach ($fields as $field) {
                $headers[] = $field->name;
            }
            fputcsv($file, $headers);

            // Escribir datos
            if ($result->num_rows > 0) {
                // Volver al inicio del resultado
                $result->data_seek(0);

                while ($row = $result->fetch_assoc()) {
                    fputcsv($file, $row);
                }
            }

            fclose($file);
            $exported_tables[] = $table;
        }

        // Crear archivo de metadatos
        $metadata = [
            'fecha_exportacion' => $timestamp,
            'base_datos' => $this->db_name,
            'tablas_exportadas' => $exported_tables
        ];

        file_put_contents($export_dir . "/metadata.json", json_encode($metadata, JSON_PRETTY_PRINT));

        return [
            "success" => true,
            "message" => "Exportación completada",
            "directorio" => $export_dir,
            "tablas_exportadas" => count($exported_tables)
        ];
    }

    public function insertarUsuario($profesion, $edad, $genero, $pericia_informatica) {
        $stmt = $this->conn->prepare("INSERT INTO usuarios (profesion, edad, genero, pericia_informatica) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $profesion, $edad, $genero, $pericia_informatica);

        if ($stmt->execute()) {
            return ["success" => true, "insert_id" => $stmt->insert_id];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }

    public function insertarTestUsabilidad($dispositivo, $tiempo, $tarea, $comentarios, $mejora, $valoracion) {

        if ($valoracion < 0 || $valoracion > 10) {
            return ["success" => false, "error" => "La valoración debe estar entre 0 y 10"];
        }

        $usuario_id = $this->getUltimoUsuario();

        if (!$usuario_id) {
            return ["success" => false, "error" => "No existe ningún usuario registrado"];
        }

        $stmt = $this->conn->prepare("
        INSERT INTO testsusabilidad
        (usuario_id, dispositivo, tiempo_completado, tarea_completada, comentarios_usuario, propuestas_mejora, valoracion)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

        $stmt->bind_param(
            "isisssi",
            $usuario_id,
            $dispositivo,
            $tiempo,
            $tarea,
            $comentarios,
            $mejora,
            $valoracion
        );

        if ($stmt->execute()) {
            return ["success" => true, "insert_id" => $stmt->insert_id];
        }

        return ["success" => false, "error" => $stmt->error];
    }


    public function insertarObservacionFacilitador($comentario) {
        // Obtener último test de usabilidad (no testpreguntas)
        $test_id = $this->getUltimoTestUsabilidad(); // Cambiado

        if (!$test_id) {
            return ["success" => false, "error" => "No existe ningún test de usabilidad registrado"];
        }

        $stmt = $this->conn->prepare("
        INSERT INTO observacionesfacilitador (test_id, comentarios_facilitador)
        VALUES (?, ?)
    ");

        $stmt->bind_param("is", $test_id, $comentario);

        if ($stmt->execute()) {
            return ["success" => true, "insert_id" => $stmt->insert_id];
        }

        return ["success" => false, "error" => $stmt->error];
    }


    public function insertarTestPreguntas($p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10) {

        // Obtener último usuario
        $usuario_id = $this->getUltimoUsuario();

        if (!$usuario_id) {
            return ["success" => false, "error" => "No existe ningún usuario registrado"];
        }

        $stmt = $this->conn->prepare("
        INSERT INTO testpreguntas 
        (usuario_id, p1, p2, p3, p4, p5, p6, p7, p8, p9, p10)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

        $stmt->bind_param(
            "issssssssss",
            $usuario_id,
            $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10
        );

        if ($stmt->execute()) {
            return ["success" => true, "insert_id" => $stmt->insert_id];
        }
        return ["success" => false, "error" => $stmt->error];
    }


    private function getUltimoUsuario() {
        $res = $this->conn->query("SELECT MAX(usuario_id) AS id FROM usuarios");
        $row = $res->fetch_assoc();
        return $row["id"];
    }

    private function getUltimoTestPreguntas() {
        $res = $this->conn->query("SELECT MAX(preguntas_id) AS id FROM testpreguntas");
        $row = $res->fetch_assoc();
        return $row["id"];
    }

    private function getUltimoTestUsabilidad() {
        $res = $this->conn->query("SELECT MAX(test_id) AS id FROM testsusabilidad");
        $row = $res->fetch_assoc();
        return $row["id"];
    }



    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

?>