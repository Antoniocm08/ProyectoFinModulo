<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsión Semanal - <?= htmlspecialchars($nombre) ?></title>
    <link rel="stylesheet" href="css/estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="contenedor">

    <a href="index.php" class="volver">← Volver a buscar</a>
    <h1> Previsión Semanal</h1>
    <h2><?= htmlspecialchars($nombre) ?>, <?= htmlspecialchars($pais) ?></h2>
    <p class="subtitulo">Próximos 5 días</p>

    <?php if (empty($porDia)): ?>
        <p class="error">Error al obtener los datos meteorológicos.</p>
    <?php else:
        $etiquetas = $maximas = $minimas = $lluvias = [];
        foreach ($porDia as $fecha => $dia) {
            $etiquetas[] = $diasSemana[date('w', strtotime($fecha))];
            $maximas[]   = round(max($dia['maximas']));
            $minimas[]   = round(min($dia['minimas']));
            $lluvias[]   = round($dia['lluvia'], 1);
        }
    ?>
        <div class="lista-dias">
            <?php foreach ($porDia as $fecha => $dia):
                $diaSemana  = $diasSemana[date('w', strtotime($fecha))];
                $fechaCorta = date('d/m', strtotime($fecha));
                $maxima     = round(max($dia['maximas']));
                $minima     = round(min($dia['minimas']));
                $humedad    = round(array_sum($dia['humedades']) / count($dia['humedades']));
                $lluvia     = round($dia['lluvia'], 1);
                $iconos = array_count_values($dia['iconos']); arsort($iconos); $icono = array_key_first($iconos);
                $descs  = array_count_values($dia['descripciones']); arsort($descs); $desc = ucfirst(array_key_first($descs));
            ?>
                <div class="tarjeta-dia">
                    <b><?= $diaSemana ?></b><br>
                    <small><?= $fechaCorta ?></small>
                    <img src="https://openweathermap.org/img/wn/<?= $icono ?>.png" alt="<?= $desc ?>">
                    <div class="subtexto"><?= $desc ?></div>
                    <div><span class="maxima">↑<?= $maxima ?>°C</span> <span class="minima">↓<?= $minima ?>°C</span></div>
                    <div class="subtexto">💧<?= $humedad ?>%<?= $lluvia > 0 ? " 🌧{$lluvia}mm" : '' ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grafica">
            <h3>🌡 Temperatura máxima y mínima (°C)</h3>
            <canvas id="graficaTemp"></canvas>
        </div>
        <div class="grafica">
            <h3>🌧 Lluvia acumulada por día (mm)</h3>
            <canvas id="graficaLluvia"></canvas>
        </div>

        <script>
        const etiquetas = <?= json_encode($etiquetas) ?>;
        new Chart(document.getElementById('graficaTemp'), {
            type: 'line',
            data: { labels: etiquetas, datasets: [
                { label: 'Máxima', data: <?= json_encode($maximas) ?>, borderColor: '#e74c3c', backgroundColor: 'rgba(231,76,60,0.15)', fill: '+1', tension: 0.4, pointRadius: 6 },
                { label: 'Mínima', data: <?= json_encode($minimas) ?>, borderColor: '#3498db', fill: false, tension: 0.4, pointRadius: 6 }
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
            <a href="acceso-prevision-horas.php?lat=<?= $latitud ?>&lon=<?= $longitud ?>&nombre=<?= urlencode($nombre) ?>&pais=<?= urlencode($pais) ?>">⏱ Por horas</a>
        </div>

    <?php endif; ?>
</div>
</body>
</html>
