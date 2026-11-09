<?php
declare(strict_types=1);

/**
 * ============================================================
 *  procesar.php — FASES 2, 3 Y 4 (backend)
 *  ------------------------------------------------------------
 *  Fase 2 · Recepción y sueldo básico ....... Bruno
 *  Fase 3 · Cálculo de haberes .............. Camila
 *  Fase 4 · Retenciones y neto .............. Virginia
 *
 *  Este archivo no genera HTML: valida, calcula y delega la
 *  presentación en resultado.view.php.
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
       FASE 2 · RECEPCIÓN Y SUELDO BÁSICO —
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
           FASE 3 · CÁLCULO DE HABERES —
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
           FASE 4 · RETENCIONES Y NETO —
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

// La vista comprueba esta constante para impedir un acceso directo por URL.
define('LIQUIDACION_APP', true);
require_once __DIR__ . '/resultado.view.php';
