<?php

// Controlador - Previsión por horas


require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/ConsultaModel.php';

class HorasController {

    public function index() {
        $latitud  = $_GET['lat']    ?? '';
        $longitud = $_GET['lon']    ?? '';
        $nombre   = $_GET['nombre'] ?? '';
        $pais     = $_GET['pais']   ?? '';

        if (!$latitud || !$longitud) {
            header('Location: index.php');
            exit;
        }

        // Llamada a la API
        $url   = API_BASE . "forecast?lat=$latitud&lon=$longitud&appid=" . API_KEY . "&units=" . UNIDADES . "&lang=" . IDIOMA . "&cnt=8";
        $datos = json_decode(@file_get_contents($url), true);

        // Guardar en base de datos
        if ($datos && $datos['cod'] == '200') {
            $model = new ConsultaModel();
            $model->guardar($nombre, $pais, $latitud, $longitud, 'horas');
        }

        // Cargar la vista
        require __DIR__ . '/../views/prevision-horas.php';
    }
}
