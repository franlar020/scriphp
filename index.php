<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liquidación de sueldos | Programación 4</title>

    <!--

    <!-- FASE 1 · Bootstrap 5 por CDN -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

    <!-- FASE 1 · Tipografías del sistema visual -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..700&amp;family=IBM+Plex+Mono:wght@400;500;600&amp;display=swap"
        rel="stylesheet"
    >

    <!-- FASE 1 · Hoja de estilos única del proyecto -->
    <link rel="stylesheet" href="assets/css/styles.css">

    <!-- FASE 1 · Vista previa del básico e impresión -->
    <script src="assets/js/app.js" defer></script>
</head>
<body>
    <main class="min-vh-100 d-flex align-items-center justify-content-center py-4 py-md-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <!-- FASE 1 · Card centrada con el formulario semántico -->
                    <section class="card liquidacion-card border-0" aria-labelledby="tituloLiquidacion">
                        <div class="card-body p-4 p-md-5">
                            <header class="mb-4">
                                <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                                    <div class="brand-mark" aria-hidden="true">L</div>
                                    <span class="project-badge">TP Programación 4</span>
                                </div>

                                <h1 id="tituloLiquidacion" class="display-title mb-2">
                                    Liquidación de sueldos
                                </h1>
                                <p class="lead-copy">
                                    Calculá el neto de un empleado según su categoría y años de antigüedad.
                                </p>
                            </header>

                            <!--
                                El formulario se envía por POST a procesar.php.
                                No usamos GET porque los datos no deben viajar en la URL.
                            -->
                            <form action="procesar.php" method="post" class="row g-4" id="liquidacionForm">
                                <!-- FASE 1 · Select de categoría -->
                                <div class="col-12">
                                    <label for="categoria" class="form-label">Categoría</label>
                                    <select
                                        class="form-select form-select-lg"
                                        id="categoria"
                                        name="categoria"
                                        required
                                        aria-describedby="categoriaHelp"
                                    >
                                        <option value="" selected disabled>Seleccioná una categoría</option>
                                        <option value="A">Categoría A</option>
                                        <option value="B">Categoría B</option>
                                        <option value="C">Categoría C</option>
                                        <option value="D">Categoría D</option>
                                    </select>
                                    <div class="form-text" id="categoriaHelp">
                                        El sueldo básico se actualiza según la categoría elegida.
                                    </div>
                                </div>

                                <!-- FASE 1 · Input numérico de antigüedad -->
                                <div class="col-12">
                                    <label for="antiguedad" class="form-label">Antigüedad</label>
                                    <div class="input-group input-group-lg">
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="antiguedad"
                                            name="antiguedad"
                                            min="1"
                                            max="30"
                                            step="1"
                                            required
                                            inputmode="numeric"
                                            placeholder="Ej. 5"
                                            aria-describedby="antiguedadHelp"
                                        >
                                        <span class="input-group-text">años</span>
                                    </div>
                                    <div class="form-text" id="antiguedadHelp">
                                        Ingresá un número entero entre 1 y 30.
                                    </div>
                                </div>

                                <!-- FASE 1 · Mejora progresiva: muestra el básico sin afectar el envío -->
                                <div class="col-12">
                                    <div class="basico-preview">
                                        <span class="preview-label">Sueldo básico</span>
                                        <output id="basicoPreview" class="preview-value" aria-live="polite">
                                            Elegí una categoría
                                        </output>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn submit-btn w-100">
                                        Calcular Liquidación
                                    </button>
                                </div>
                            </form>

                            <footer class="form-footer">
                                <span>Los importes se muestran en pesos argentinos.</span>
                                <span>Programación 4 / UNLAR</span>
                            </footer>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
