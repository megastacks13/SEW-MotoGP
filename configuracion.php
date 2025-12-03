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

            if ($result->num_rows > 0) {
                $file = fopen($filename, 'w');

                // Escribir encabezados
                $fields = $result->fetch_fields();
                $headers = [];
                foreach ($fields as $field) {
                    $headers[] = $field->name;
                }
                fputcsv($file, $headers);

                // Volver al inicio del resultado
                $result->data_seek(0);

                // Escribir datos
                while ($row = $result->fetch_assoc()) {
                    fputcsv($file, $row);
                }

                fclose($file);
                $exported_tables[] = $table;
            }
        }

        // Crear archivo de metadatos
        $metadata = [
            'fecha_exportacion' => $timestamp,
            'base_datos' => $this->db_name,
            'tablas_exportadas' => $exported_tables
        ];

        file_put_contents($export_dir . "/metadata.json", json_encode($metadata, JSON_PRETTY_PRINT));

        // Comprimir el directorio
        $this->comprimirDirectorio($export_dir, $directorio . "/backup_" . $timestamp . ".zip");

        // Eliminar directorio temporal
        $this->eliminarDirectorio($export_dir);

        return [
            "success" => true,
            "message" => "Exportación completada",
            "archivo" => $directorio . "/backup_" . $timestamp . ".zip",
            "tablas_exportadas" => count($exported_tables)
        ];
    }


    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

?>