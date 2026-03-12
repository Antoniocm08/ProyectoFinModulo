<?php

// Controlador - Búsqueda de ciudades


require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/ConsultaModel.php';

class BuscadorController {

    public function index() {
        $error    = '';
        $ciudades = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ciudad'])) {
            $nombreCiudad = trim($_POST['ciudad']);
            $url      = "https://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($nombreCiudad) . "&limit=5&appid=" . API_KEY;
            $respuesta = @file_get_contents($url);
            $ciudades  = $respuesta ? json_decode($respuesta, true) : [];

            if (empty($ciudades)) {
                $error = "Ciudad no encontrada: <b>" . htmlspecialchars($nombreCiudad) . "</b>. Prueba con otro nombre.";
            }
        }

        // Cargar la vista pasando los datos
        require __DIR__ . '/../views/buscador.php';
    }
}
