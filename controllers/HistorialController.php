<?php

// Controlador - Historial de consultas


require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/ConsultaModel.php';

class HistorialController {

    public function index() {
        // Obtener datos del modelo
        $model     = new ConsultaModel();
        $historial = $model->obtenerTodas();

        // Cargar la vista
        require __DIR__ . '/../views/consultas-realizadas.php';
    }
}
