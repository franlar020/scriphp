<?php
/**
 * FASE 4 · Vista del comprobante y del estado de error — Virginia.
 * Solo se carga desde procesar.php.
 */
if (!defined('LIQUIDACION_APP')) {
    http_response_code(404);
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Programación 4</title>

    <!-- FASE 4 · Vista: Bootstrap 5 por CDN -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

    <!-- FASE 4 · Tipografías del sistema visual -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..700&amp;family=IBM+Plex+Mono:wght@400;500;600&amp;display=swap"
        rel="stylesheet"
    >

    <!-- FASE 4 · Hoja de estilos única del proyecto -->
    <link rel="stylesheet" href="assets/css/styles.css">

    <!-- FASE 4 · JS del frontend (impresión del comprobante) -->
    <script src="assets/js/app.js" defer></script>
</head>
<body>
    <main class="min-vh-100 d-flex flex-column align-items-center justify-content-center py-4 py-md-5">
        <div class="container result-shell">
            <?php if ($hayError): ?>
                <!-- FASE 4 · Vista de error: validación del servidor -->
                <section class="card result-card error-card border-0" aria-labelledby="errorTitle">
                    <div class="card-body p-4 p-md-5">
                        <div class="error-icon" aria-hidden="true">!</div>
                        <p class="result-kicker">No se pudo calcular</p>
                        <h1 id="errorTitle" class="result-title">Revisá los datos ingresados</h1>
                        <p class="result-copy">
                            El formulario llegó con datos inválidos o incompletos. Corregí lo siguiente y volvé a intentar.
                        </p>

                        <ul class="error-list">
                            <?php foreach ($errores as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <a href="index.php" class="btn btn-back">Volver al formulario</a>
                    </div>
                </section>
            <?php else: ?>
                <!--
                    FASE 4 · Vista del comprobante
                    Todos los importes se formatean con number_format($valor, 2, ',', '.').
                -->
                <section class="card result-card receipt-card border-0" aria-labelledby="receiptTitle">
                    <div class="receipt-head">
                        <div>
                            <p class="result-kicker">Comprobante de liquidación</p>
                            <h1 id="receiptTitle" class="result-title">Liquidación de haberes</h1>
                        </div>
                        <span class="stamp" aria-hidden="true">Liquidado</span>
                    </div>

                    <div class="receipt-body">
                        <!-- FASE 4 · Datos del comprobante -->
                        <dl class="receipt-meta">
                            <div>
                                <dt>Categoría</dt>
                                <dd><?= e($categoria) ?></dd>
                            </div>
                            <div>
                                <dt>Antigüedad</dt>
                                <dd><?= (int) $antiguedad ?> años</dd>
                            </div>
                            <div>
                                <dt>Fecha</dt>
                                <dd><?= e($fechaLiquidacion) ?></dd>
                            </div>
                        </dl>

                        <!-- FASE 4 · Tabla: haberes, retenciones y neto -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle mb-0">
                                <caption class="visually-hidden">
                                    Detalle de haberes y descuentos de la liquidación
                                </caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Concepto</th>
                                        <th scope="col" class="text-end">Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">
                                            Sueldo básico
                                            <span class="concept-detail">Categoría <?= e($categoria) ?></span>
                                        </th>
                                        <td class="amount">$<?= number_format($sueldo_basico, 2, ',', '.') ?></td>
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            Bonificación por antigüedad
                                            <span class="concept-detail">10 % del básico por año</span>
                                        </th>
                                        <td class="amount">$<?= number_format($bonif_antiguedad, 2, ',', '.') ?></td>
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            Bonificación por presentismo
                                            <span class="concept-detail">15 % del básico</span>
                                        </th>
                                        <td class="amount">$<?= number_format($bonif_presentismo, 2, ',', '.') ?></td>
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            Bonificación por dedicación especial
                                            <span class="concept-detail">50 % del básico</span>
                                        </th>
                                        <td class="amount">$<?= number_format($bonif_dedicacion, 2, ',', '.') ?></td>
                                    </tr>

                                    <tr class="row-subtotal">
                                        <th scope="row">Total haberes</th>
                                        <td class="amount">$<?= number_format($total_haberes, 2, ',', '.') ?></td>
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            Obra social
                                            <span class="concept-detail">5 % del total de haberes</span>
                                        </th>
                                        <td class="amount amount--discount">-$<?= number_format($obra_social, 2, ',', '.') ?></td>
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            ANSES (Jubilación)
                                            <span class="concept-detail">11 % del total de haberes</span>
                                        </th>
                                        <td class="amount amount--discount">-$<?= number_format($anses, 2, ',', '.') ?></td>
                                    </tr>

                                    <tr class="row-subtotal">
                                        <th scope="row">Total descuentos</th>
                                        <td class="amount amount--discount">-$<?= number_format($total_descuentos, 2, ',', '.') ?></td>
                                    </tr>

                                    <tr class="row-net">
                                        <th scope="row">Total neto</th>
                                        <td class="amount">$<?= number_format($total_neto, 2, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="receipt-actions">
                            <a href="index.php" class="btn btn-back">Nueva liquidación</a>
                            <button type="button" class="btn btn-print" id="printButton">
                                Imprimir comprobante
                            </button>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <footer class="page-footer">
                Programación 4 / UNLAR · Trabajo Práctico de Liquidación de Sueldos
            </footer>
        </div>
    </main>
</body>
</html>
