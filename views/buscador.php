<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicación del Tiempo</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="contenedor">

    <h1>🌤 Aplicación del Tiempo</h1>
    <p class="subtitulo">Consulta el tiempo en cualquier ciudad del mundo</p>

    <form method="POST" action="index.php">
        <div class="buscador">
            <input type="text" name="ciudad"
                placeholder="Escribe el nombre de una ciudad..."
                value="<?= isset($_POST['ciudad']) ? htmlspecialchars($_POST['ciudad']) : '' ?>"
                required autofocus>
            <button type="submit">🔍 Buscar</button>
        </div>
    </form>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php foreach ($ciudades as $ciudad):
        $p = "lat={$ciudad['lat']}&lon={$ciudad['lon']}&nombre=" . urlencode($ciudad['name']) . "&pais=" . urlencode($ciudad['country']);
    ?>
        <div class="tarjeta-ciudad">
            <div class="info-ciudad">
                <b><?= htmlspecialchars($ciudad['name']) ?></b>
                <?= !empty($ciudad['state']) ? ', ' . htmlspecialchars($ciudad['state']) : '' ?>
                — <?= htmlspecialchars($ciudad['country']) ?>
                <br>
                <small>Lat: <?= round($ciudad['lat'], 4) ?> | Lon: <?= round($ciudad['lon'], 4) ?></small>
            </div>
            <div class="acciones">
                <a href="acceso-tiempo-ahora.php?<?= $p ?>">🌡 Actual</a>
                <a href="acceso-prevision-horas.php?<?= $p ?>">⏱ Por horas</a>
                <a href="acceso-prevision-semanal.php?<?= $p ?>">📅 Semanal</a>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="enlace-historial">
        <a href="acceso-consultas-realizadas.php"> Ver historial de tus búsquedas</a>
    </div>

</div>
</body>
</html>
