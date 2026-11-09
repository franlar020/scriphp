<?php
declare(strict_types=1);

/**
 * ============================================================
 *  procesar.php — FASES 2, 3 Y 4
 *  ------------------------------------------------------------
 *  Fase 2 · Recepción y sueldo básico ....... Bruno
 *  Fase 3 · Cálculo de haberes .............. Camila
 *  Fase 4 · Retenciones, neto y vista ....... Virginia
 *
 *  Caso de aceptación:
 *  Categoría A + 9 años => Total neto $8.676,27
 *
 *  Política de redondeo:
 *  - El Total de Haberes se redondea una sola vez a 2 decimales.
 *  - Cada retención se redondea a 2 decimales antes de sumar.
 *  - El Total Neto se obtiene restando los descuentos redondeados.
 * ============================================================
 */

date_default_timezone_set('America/Argentina/Buenos_Aires');
header('Content-Type: text/html; charset=UTF-8');

/**
 * Escapa texto dinámico antes de imprimirlo en HTML.
 */
function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

$errores = [];
$categoria = null;
$antiguedad = null;

$sueldo_basico = 0.0;
$bonif_antiguedad = 0.0;
$bonif_presentismo = 0.0;
$bonif_dedicacion = 0.0;
$total_haberes = 0.0;
$obra_social = 0.0;
$anses = 0.0;
$total_descuentos = 0.0;
$total_neto = 0.0;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    /* Acceso directo por GET: no hay datos para procesar. */
    http_response_code(405);
    $errores[] = 'Este procesador solo acepta envíos por POST. Volvé al formulario para calcular una liquidación.';
} else {
    /* ========================================================
       FASE 2 · RECEPCIÓN Y SUELDO BÁSICO — Bruno
       ======================================================== */

    // Captura de los datos enviados por el formulario.
    $categoria_raw = $_POST['categoria'] ?? '';
    $antiguedad_raw = $_POST['antiguedad'] ?? '';

    // Validación de la categoría: solo se aceptan A, B, C o D.
    if (!is_string($categoria_raw) || !in_array($categoria_raw, ['A', 'B', 'C', 'D'], true)) {
        $errores[] = 'La categoría debe ser A, B, C o D.';
    } else {
        $categoria = $categoria_raw;
    }

    // Validación de la antigüedad: entero entre 1 y 30.
    $antiguedad_validada = is_string($antiguedad_raw)
        ? filter_var($antiguedad_raw, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
                'max_range' => 30,
            ],
        ])
        : false;

    if ($antiguedad_validada === false) {
        $errores[] = 'La antigüedad debe ser un número entero entre 1 y 30.';
    } else {
        $antiguedad = $antiguedad_validada;
    }

    if (empty($errores)) {
        // match: evalúa la categoría y devuelve el sueldo básico correspondiente.
        // La validación anterior garantiza que solo pueda entrar A, B, C o D.
        $sueldo_basico = match ($categoria) {
            'A' => 4050.55,
            'B' => 3209.20,
            'C' => 2575.73,
            'D' => 1812.28,
        };

        /* ====================================================
           FASE 3 · CÁLCULO DE HABERES — Camila
           ==================================================== */

        // Antigüedad: 10% del básico por cada año trabajado.
        $bonif_antiguedad = $sueldo_basico * 0.10 * $antiguedad;

        // Presentismo: 15% del básico.
        $bonif_presentismo = $sueldo_basico * 0.15;

        // Dedicación especial: 50% del básico.
        $bonif_dedicacion = $sueldo_basico * 0.50;

        // Consolidación: básico + las tres bonificaciones.
        // Se redondea una sola vez para que las retenciones partan de una base limpia.
        $total_haberes = round(
            $sueldo_basico
            + $bonif_antiguedad
            + $bonif_presentismo
            + $bonif_dedicacion,
            2
        );

        /* ====================================================
           FASE 4 · RETENCIONES Y NETO — Virginia
           ==================================================== */

        // Las retenciones se calculan sobre el TOTAL DE HABERES, no sobre el básico.
        // Obra social: 5% del total de haberes.
        $obra_social = round($total_haberes * 0.05, 2);

        // ANSES (Jubilación): 11% del total de haberes.
        $anses = round($total_haberes * 0.11, 2);

        // Suma de retenciones y cálculo final del neto.
        $total_descuentos = $obra_social + $anses;
        $total_neto = $total_haberes - $total_descuentos;
    } else {
        http_response_code(400);
    }
}

$hayError = !empty($errores);
$pageTitle = $hayError ? 'Error en la liquidación' : 'Comprobante de liquidación';
$fechaLiquidacion = date('d/m/Y');
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
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..700&family=IBM+Plex+Mono:wght@400;500;600&display=swap"
        rel="stylesheet"
    >

    <style>
        /* FASE 4 · Tokens del proyecto: mantener sincronizados con index.php */
        :root {
            --paper: #E9EDF0;
            --surface: #FCFDFC;
            --ink: #15211D;
            --brand: #0B6B4F;
            --brand-dark: #07503B;
            --brand-soft: #E8F3EE;
            --stamp: #B3402A;
            --line: #C9D2CE;
            --muted: #52605A;
            --font-sans: "Bricolage Grotesque", system-ui, -apple-system, "Segoe UI", sans-serif;
            --font-mono: "IBM Plex Mono", "Consolas", monospace;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            font-family: var(--font-sans);
            background-color: var(--paper);
            background-image:
                radial-gradient(circle at 15% 10%, rgba(11, 107, 79, 0.08), transparent 28rem),
                linear-gradient(rgba(21, 33, 29, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(21, 33, 29, 0.035) 1px, transparent 1px);
            background-size: auto, 28px 28px, 28px 28px;
        }

        .result-shell {
            width: 100%;
            max-width: 860px;
        }

        .result-card {
            position: relative;
            overflow: hidden;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow:
                0 30px 80px -45px rgba(21, 33, 29, 0.45),
                0 1px 0 rgba(255, 255, 255, 0.8) inset;
            animation: card-in 600ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        .result-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: var(--brand);
        }

        .error-card::before {
            background: var(--stamp);
        }

        @keyframes card-in {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-kicker {
            margin: 0 0 0.35rem;
            color: var(--brand);
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .error-card .result-kicker {
            color: var(--stamp);
        }

        .result-title {
            margin: 0;
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.045em;
        }

        .result-copy {
            max-width: 60ch;
            margin: 0.9rem 0 0;
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.55;
        }

        /* FASE 4 · Encabezado del comprobante */
        .receipt-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1.5rem;
            padding: 2rem 2rem 1.25rem;
            border-bottom: 1px dashed var(--line);
        }

        .stamp {
            margin-top: 0.25rem;
            padding: 0.4rem 0.7rem;
            border: 2px solid var(--brand);
            border-radius: 6px;
            outline: 1px solid var(--brand);
            outline-offset: 2px;
            color: var(--brand);
            font-family: var(--font-mono);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            white-space: nowrap;
            opacity: 0.9;
            transform: rotate(-6deg);
            animation: stamp-in 500ms 250ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        @keyframes stamp-in {
            from {
                opacity: 0;
                transform: rotate(-10deg) scale(1.08);
            }
            to {
                opacity: 0.9;
                transform: rotate(-6deg) scale(1);
            }
        }

        .receipt-body {
            padding: 1.25rem 2rem 2rem;
        }

        /* FASE 4 · Datos de la liquidación */
        .receipt-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
            margin: 0 0 1.25rem;
            padding: 0.85rem 1rem;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #F5F8F6;
        }

        .receipt-meta dt {
            margin: 0 0 0.15rem;
            color: #6B7872;
            font-size: 0.68rem;
            font-weight: 600;
        }

        .receipt-meta dd {
            margin: 0;
            color: var(--ink);
            font-family: var(--font-mono);
            font-size: 0.86rem;
            font-weight: 500;
            font-variant-numeric: tabular-nums;
        }

        /* FASE 4 · Tabla del comprobante */
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--ink);
            margin-bottom: 0;
            border-color: var(--line);
            font-size: 0.9rem;
        }

        .table thead th {
            padding: 0.75rem 0.9rem;
            border-color: var(--brand) !important;
            background-color: var(--brand) !important;
            color: #fff !important;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .table tbody th,
        .table tbody td {
            padding: 0.8rem 0.9rem;
            border-color: var(--line);
            vertical-align: middle;
        }

        .table tbody th {
            color: var(--ink);
            font-weight: 500;
            text-align: left;
        }

        .concept-detail {
            display: block;
            margin-top: 0.18rem;
            color: #6B7872;
            font-size: 0.7rem;
            font-weight: 400;
            line-height: 1.3;
        }

        .amount {
            color: var(--ink);
            font-family: var(--font-mono);
            font-weight: 500;
            font-variant-numeric: tabular-nums;
            text-align: right;
            white-space: nowrap;
        }

        .amount--discount {
            color: var(--stamp);
        }

        .table .row-subtotal > * {
            background-color: #F1F5F2 !important;
            font-weight: 600;
        }

        .table .row-net > * {
            border-color: var(--brand) !important;
            background-color: var(--brand) !important;
            color: #fff !important;
            font-size: 1rem;
            font-weight: 700;
        }

        .table .row-net .amount {
            color: #fff !important;
            font-size: 1.08rem;
        }

        /* FASE 4 · Nota de redondeo para la defensa */
        .rounding-note {
            margin: 1rem 0 0;
            padding: 0.8rem 1rem;
            border-left: 3px solid var(--stamp);
            border-radius: 0 8px 8px 0;
            background: #FBEDE9;
            color: #6B3A2F;
            font-size: 0.75rem;
            line-height: 1.5;
        }

        .receipt-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .btn-back,
        .btn-print {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            font-family: var(--font-sans);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease;
        }

        .btn-back {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
        }

        .btn-back:hover {
            border-color: var(--brand);
            background: var(--brand-soft);
            color: var(--brand-dark);
        }

        .btn-print {
            border: 1px solid var(--brand);
            background: var(--brand);
            color: #fff;
            cursor: pointer;
        }

        .btn-print:hover {
            border-color: var(--brand-dark);
            background: var(--brand-dark);
            color: #fff;
        }

        .btn-back:focus-visible,
        .btn-print:focus-visible {
            outline: 3px solid rgba(11, 107, 79, 0.35);
            outline-offset: 3px;
        }

        /* FASE 4 · Vista de error */
        .error-icon {
            display: grid;
            place-items: center;
            width: 48px;
            height: 48px;
            margin-bottom: 1rem;
            border-radius: 50%;
            background: #FBEDE9;
            color: var(--stamp);
            font-family: var(--font-mono);
            font-size: 1.35rem;
            font-weight: 700;
        }

        .error-list {
            margin: 1rem 0 1.5rem;
            padding-left: 1.2rem;
            color: #6B3A2F;
            font-size: 0.9rem;
            line-height: 1.55;
        }

        .error-list li + li {
            margin-top: 0.35rem;
        }

        .page-footer {
            margin-top: 1.25rem;
            color: #6B7872;
            font-size: 0.7rem;
            text-align: center;
        }

        @media (max-width: 575.98px) {
            .receipt-head {
                flex-direction: column;
                padding: 1.5rem 1.25rem 1rem;
            }

            .stamp {
                transform: rotate(-3deg);
            }

            .receipt-body {
                padding: 1rem 1.25rem 1.5rem;
            }

            .receipt-meta {
                grid-template-columns: 1fr;
            }

            .table {
                font-size: 0.8rem;
            }

            .table tbody th,
            .table tbody td {
                padding: 0.65rem 0.6rem;
            }

            .receipt-actions .btn {
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .result-card,
            .stamp {
                animation: none;
            }

            .btn-back,
            .btn-print {
                transition: none;
            }
        }

        @media print {
            @page {
                margin: 1.5cm;
            }

            body {
                background: #fff;
            }

            .result-shell {
                max-width: 100%;
            }

            .result-card {
                border: 1px solid var(--line);
                border-radius: 0;
                box-shadow: none;
                animation: none;
            }

            .result-card::before,
            .receipt-actions,
            .page-footer,
            .stamp {
                display: none !important;
            }

            .receipt-head {
                border-bottom: 1px dashed var(--line);
            }

            .table {
                font-size: 9.5pt;
            }

            .table .row-net > * {
                background-color: var(--brand-soft) !important;
                color: var(--ink) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table .row-net .amount {
                color: var(--ink) !important;
            }

            .rounding-note {
                break-inside: avoid;
            }
        }
    </style>
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

                        <!--
                            Nota de defensa: la suma de las bonificaciones mostradas
                            puede diferir en $0,01 del Total de Haberes por redondeo
                            independiente de cada línea. El total se calcula sobre la
                            suma exacta y se redondea una sola vez.
                        -->
                        <p class="rounding-note">
                            Los importes parciales se muestran redondeados a 2 decimales. El Total de Haberes
                            se calcula sobre la suma exacta y se redondea una sola vez, por eso puede diferir
                            en $0,01 de la suma de las líneas mostradas.
                        </p>

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

    <script>
        // FASE 4 · Impresión del comprobante (solo si el botón existe).
        document.getElementById("printButton")?.addEventListener("click", () => {
            window.print();
        });
    </script>
</body>
</html>
