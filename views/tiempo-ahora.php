<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiempo Actual - <?= htmlspecialchars($nombre) ?></title>
    <link rel="stylesheet" href="css/estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="contenedor">

    <a href="index.php" class="volver">← Volver a buscar</a>
    <h1>🌡 Tiempo Actual</h1>
    <h2><?= htmlspecialchars($nombre) ?>, <?= htmlspecialchars($pais) ?></h2>

    <?php if (!$datos || $datos['cod'] != 200): ?>
        <p class="error">Error al obtener los datos meteorológicos.</p>
    <?php else:
        $temperatura = round($datos['main']['temp']);
        $sensacion   = round($datos['main']['feels_like']);
        $descripcion = ucfirst($datos['weather'][0]['description']);
        $icono       = $datos['weather'][0]['icon'];
        $humedad     = $datos['main']['humidity'];
        $presion     = $datos['main']['pressure'];
        $viento      = round($datos['wind']['speed'] * 3.6, 1);
        $dirs        = ['N','NE','E','SE','S','SO','O','NO'];
        $dirViento   = $dirs[round(($datos['wind']['deg'] ?? 0) / 45) % 8];
        $nubes       = $datos['clouds']['all'];
        $visibilidad = isset($datos['visibility']) ? round($datos['visibility'] / 1000, 1) : 'N/D';
        $amanecer    = date('H:i', $datos['sys']['sunrise']);
        $atardecer   = date('H:i', $datos['sys']['sunset']);
        $lluvia      = $datos['rain']['1h'] ?? 0;
    ?>

        <div class="tiempo-principal">
            <img src="https://openweathermap.org/img/wn/<?= $icono ?>@2x.png" alt="<?= $descripcion ?>">
            <div class="temperatura-grande"><?= $temperatura ?>°C</div>
            <div class="descripcion"><?= $descripcion ?></div>
            <div class="subtexto">Sensación térmica: <?= $sensacion ?>°C</div>
        </div>

        <div class="cuadricula-datos">
            <div class="dato">💧 Humedad<br><b><?= $humedad ?>%</b></div>
            <div class="dato">🌬 Viento<br><b><?= $viento ?> km/h <?= $dirViento ?></b></div>
            <div class="dato">📊 Presión<br><b><?= $presion ?> hPa</b></div>
            <div class="dato">👁 Visibilidad<br><b><?= $visibilidad ?> km</b></div>
            <div class="dato">☁ Nubosidad<br><b><?= $nubes ?>%</b></div>
            <div class="dato">🌅 Amanecer<br><b><?= $amanecer ?></b></div>
            <div class="dato">🌇 Atardecer<br><b><?= $atardecer ?></b></div>
            <?php if ($lluvia > 0): ?>
            <div class="dato">🌧 Lluvia <br><b><?= $lluvia ?> mm</b></div>
            <?php endif; ?>
        </div>

        <div class="grafica">
            <h3>Resumen del tiempo atmosférico actual</h3>
            <canvas id="graficaActual"></canvas>
        </div>

        <script>
        new Chart(document.getElementById('graficaActual'), {
            type: 'bar',
            data: {
                labels: ['Temperatura (°C)', 'Sensación (°C)', 'Humedad (%)', 'Nubosidad (%)'],
                datasets: [{ data: [<?= $temperatura ?>, <?= $sensacion ?>, <?= $humedad ?>, <?= $nubes ?>],
                    backgroundColor: ['#e74c3c','#e67e22','#3498db','#9b59b6'], borderRadius: 6 }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
        </script>

        <div class="navegacion">
            <a href="acceso-prevision-horas.php?lat=<?= $latitud ?>&lon=<?= $longitud ?>&nombre=<?= urlencode($nombre) ?>&pais=<?= urlencode($pais) ?>">⏱ Ver por horas</a>
            <a href="acceso-prevision-semanal.php?lat=<?= $latitud ?>&lon=<?= $longitud ?>&nombre=<?= urlencode($nombre) ?>&pais=<?= urlencode($pais) ?>">📅 Ver la semana</a>
        </div>

    <?php endif; ?>
</div>
</body>
</html>
