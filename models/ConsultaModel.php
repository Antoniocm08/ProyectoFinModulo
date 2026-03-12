<?php

// Modelo - Gestiona el acceso a la base de datos
// Esta incluido la base de datos utilizando PHP DAO


class ConsultaModel {

    private $conexion;

    public function __construct() {
        $host = getenv('DB_HOST') ?: 'db';
        $bd   = getenv('DB_NAME') ?: 'apptiempo';
        $user = getenv('DB_USER') ?: 'antonio';
        $pass = getenv('DB_PASS') ?: 'antonio';

        try {
            $this->conexion = new PDO(
                "mysql:host=$host;dbname=$bd;charset=utf8",
                $user, $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->crearTabla();
        } catch (PDOException $e) {
            $this->conexion = null;
        }
    }

    // Crea la tabla si no existe
    private function crearTabla() {
        $this->conexion->exec("CREATE TABLE IF NOT EXISTS consultas (
            id       INT AUTO_INCREMENT PRIMARY KEY,
            ciudad   VARCHAR(100) NOT NULL,
            pais     VARCHAR(10)  NOT NULL,
            latitud  DECIMAL(9,6) NOT NULL,
            longitud DECIMAL(9,6) NOT NULL,
            tipo     VARCHAR(20)  NOT NULL,
            fecha    DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }

    // Guarda una consulta en la base de datos
    public function guardar($ciudad, $pais, $latitud, $longitud, $tipo) {
        if (!$this->conexion) return;
        $stmt = $this->conexion->prepare(
            "INSERT INTO consultas (ciudad, pais, latitud, longitud, tipo) VALUES (?,?,?,?,?)"
        );
        $stmt->execute([$ciudad, $pais, $latitud, $longitud, $tipo]);
    }

    // Obtiene todas las consultas
    public function obtenerTodas($limite = 100) {
        if (!$this->conexion) return [];
        $limite = (int) $limite;
        return $this->conexion->query(
            "SELECT * FROM consultas ORDER BY fecha DESC LIMIT $limite"
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}
