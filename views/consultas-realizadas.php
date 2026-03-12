<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Consultas</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="contenedor">

    <a href="index.php" class="volver">← Volver a buscar</a>
    <h1> Historial de tus búsquedas</h1>
    <p class="subtitulo">Últimas 100 búsquedas realizadas en la aplicación</p>

    <?php
    $tiposConsulta = ['actual' => '🌡 Actual', 'horas' => '⏱ Por horas', 'semana' => '📅 Semanal'];
    if (empty($historial)): ?>
        <p class="error">Aún no hay consultas registradas. ¡Busca una ciudad!</p>
    <?php else: ?>
        <p><?= count($historial) ?> consultas registradas</p>
        <table class="tabla-historial">
            <thead>
                <tr>
                    <th>Ciudad</th>
                    <th>País</th>
                    <th>Tipo</th>
                    <th>Fecha y hora</th>
                    <th>Ver de nuevo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial as $fila):
                    $p = "lat={$fila['latitud']}&lon={$fila['longitud']}&nombre=" . urlencode($fila['ciudad']) . "&pais=" . urlencode($fila['pais']);
                ?>
                <tr>
                    <td><?= htmlspecialchars($fila['ciudad']) ?></td>
                    <td><?= htmlspecialchars($fila['pais']) ?></td>
                    <td><?= $tiposConsulta[$fila['tipo']] ?? $fila['tipo'] ?></td>
                    <td><?= $fila['fecha'] ?></td>
                    <td>
                        <a href="acceso-tiempo-ahora.php?<?= $p ?>">🌡</a>
                        <a href="acceso-prevision-horas.php?<?= $p ?>">⏱</a>
                        <a href="acceso-prevision-semanal.php?<?= $p ?>">📅</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>
