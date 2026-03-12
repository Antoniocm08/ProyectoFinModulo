<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsión por Horas - <?= htmlspecialchars($nombre) ?></title>
    <link rel="stylesheet" href="css/estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="contenedor">

    <a href="index.php" class="volver">← Volver a buscar</a>
    <h1> Previsión por Horas</h1>
    <h2><?= htmlspecialchars($nombre) ?>, <?= htmlspecialchars($pais) ?></h2>
    <p class="subtitulo">Próximas 24 horas</p>

    <?php if (!$datos || $datos['cod'] != '200'): ?>
        <p class="error">Error al obtener los datos meteorológicos.</p>
    <?php else:
        $etiquetas = $temperaturas = $sensaciones = $lluvias = [];
    ?>
        <div class="lista-horas">
            <?php foreach ($datos['list'] as $intervalo):
                $hora        = date('H:i', $intervalo['dt']);
                $temperatura = round($intervalo['main']['temp']);
                $sensacion   = round($intervalo['main']['feels_like']);
                $descripcion = ucfirst($intervalo['weather'][0]['description']);
                $icono       = $intervalo['weather'][0]['icon'];
                $humedad     = $intervalo['main']['humidity'];
                $viento      = round($intervalo['wind']['speed'] * 3.6, 1);
                $lluvia      = $intervalo['rain']['3h'] ?? 0;
                $etiquetas[]    = $hora;
                $temperaturas[] = $temperatura;
                $sensaciones[]  = $sensacion;
                $lluvias[]      = $lluvia;
            ?>
                <div class="tarjeta-hora">
                    <b><?= $hora ?></b>
                    <img src="https://openweathermap.org/img/wn/<?= $icono ?>.png" alt="<?= $descripcion ?>">
                    <div class="temperatura-hora"><?= $temperatura ?>°C</div>
                    <div class="subtexto"><?= $descripcion ?></div>
                    <div class="subtexto">💧<?= $humedad ?>% 🌬<?= $viento ?>km/h</div>
                    <?php if ($lluvia > 0): ?><div class="subtexto">🌧<?= $lluvia ?>mm</div><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grafica">
            <h3>🌡 Temperatura por horas (°C)</h3>
            <canvas id="graficaTemp"></canvas>
        </div>
        <div class="grafica">
            <h3>🌧 Precipitación estimada (mm)</h3>
            <canvas id="graficaLluvia"></canvas>
        </div>

        <script>
        const etiquetas = <?= json_encode($etiquetas) ?>;
        new Chart(document.getElementById('graficaTemp'), {
            type: 'line',
            data: { labels: etiquetas, datasets: [
                { label: 'Temperatura', data: <?= json_encode($temperaturas) ?>, borderColor: '#e74c3c', backgroundColor: 'rgba(231,76,60,0.15)', fill: true, tension: 0.4, pointRadius: 5 },
                { label: 'Sensación',   data: <?= json_encode($sensaciones) ?>,  borderColor: '#e67e22', borderDash: [5,5], fill: false, tension: 0.4, pointRadius: 3 }
            ]},
            options: { scales: { y: { beginAtZero: false } } }
        });
        new Chart(document.getElementById('graficaLluvia'), {
            type: 'bar',
            data: { labels: etiquetas, datasets: [{ label: 'Lluvia (mm)', data: <?= json_encode($lluvias) ?>, backgroundColor: 'rgba(52,152,219,0.7)', borderRadius: 4 }]},
            options: { scales: { y: { beginAtZero: true } } }
        });
        </script>

        <div class="navegacion">
            <a href="acceso-tiempo-ahora.php?lat=<?= $latitud ?>&lon=<?= $longitud ?>&nombre=<?= urlencode($nombre) ?>&pais=<?= urlencode($pais) ?>">🌡 Tiempo ahora</a>
            <a href="acceso-prevision-semanal.php?lat=<?= $latitud ?>&lon=<?= $longitud ?>&nombre=<?= urlencode($nombre) ?>&pais=<?= urlencode($pais) ?>">📅 Ver la semana</a>
        </div>

    <?php endif; ?>
</div>
</body>
</html>
