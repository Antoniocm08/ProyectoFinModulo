<?php

// Controlador - Previsión semanal


require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/ConsultaModel.php';

class SemanaController {

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
        $url   = API_BASE . "forecast?lat=$latitud&lon=$longitud&appid=" . API_KEY . "&units=" . UNIDADES . "&lang=" . IDIOMA . "&cnt=40";
        $datos = json_decode(@file_get_contents($url), true);

        // Guardar en base de datos
        if ($datos && $datos['cod'] == '200') {
            $model = new ConsultaModel();
            $model->guardar($nombre, $pais, $latitud, $longitud, 'semana');
        }

        // Agrupar datos por día
        $diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
        $porDia = [];
        if ($datos && $datos['cod'] == '200') {
            foreach ($datos['list'] as $intervalo) {
                $dia = date('Y-m-d', $intervalo['dt']);
                $porDia[$dia]['maximas'][]       = $intervalo['main']['temp_max'];
                $porDia[$dia]['minimas'][]        = $intervalo['main']['temp_min'];
                $porDia[$dia]['iconos'][]         = $intervalo['weather'][0]['icon'];
                $porDia[$dia]['descripciones'][]  = $intervalo['weather'][0]['description'];
                $porDia[$dia]['lluvia']           = ($porDia[$dia]['lluvia'] ?? 0) + ($intervalo['rain']['3h'] ?? 0);
                $porDia[$dia]['humedades'][]      = $intervalo['main']['humidity'];
            }
        }

        // Cargar la vista
        require __DIR__ . '/../views/prevision-semanal.php';
    }
}
