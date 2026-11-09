<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liquidación de sueldos | Programación 4</title>

    <!--
    ============================================================
      FASE 1 · INFRAESTRUCTURA, FRONTEND Y FORMULARIO
      Responsable: Francisco
      Archivo: index.php
      Sección: desde <html> hasta el cierre de </form>

      Puntos para la defensa:
      - Bootstrap 5 por CDN y Card centrada.
      - Formulario semántico con envío POST a procesar.php.
      - Select de categoría A-D e input numérico de 1 a 30.
      - DeepSeek 9B local + Ollama: privacidad, modo offline y control.
      - PHP centralizado con XAMPP + ngrok para probar en equipo.
    ============================================================
    -->

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
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..700&family=IBM+Plex+Mono:wght@400;500;600&display=swap"
        rel="stylesheet"
    >

    <style>
        /* FASE 1 · Tokens del proyecto: mantener sincronizados con procesar.php */
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

        /* FASE 1 · Card principal */
        .liquidacion-card {
            position: relative;
            overflow: hidden;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 22px;
            box-shadow:
                0 30px 80px -45px rgba(21, 33, 29, 0.45),
                0 1px 0 rgba(255, 255, 255, 0.8) inset;
            animation: card-in 600ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        .liquidacion-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: var(--brand);
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

        .brand-mark {
            display: grid;
            place-items: center;
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--ink);
            color: #fff;
            font-family: var(--font-mono);
            font-size: 1.05rem;
            font-weight: 600;
            box-shadow: 0 8px 18px -12px rgba(21, 33, 29, 0.85);
        }

        .project-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.42rem 0.7rem;
            border: 1px solid rgba(11, 107, 79, 0.18);
            border-radius: 999px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1;
        }

        .display-title {
            margin: 0;
            font-size: clamp(2.1rem, 5vw, 2.9rem);
            font-weight: 700;
            line-height: 0.98;
            letter-spacing: -0.045em;
        }

        .lead-copy {
            max-width: 42ch;
            margin: 0;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.55;
        }

        /* FASE 1 · Formulario */
        .form-label {
            margin-bottom: 0.45rem;
            color: var(--ink);
            font-size: 0.82rem;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            padding: 0.8rem 0.95rem;
            border: 1px solid var(--line);
            border-radius: 12px;
            background-color: #fff;
            color: var(--ink);
            font-family: var(--font-sans);
            font-size: 0.98rem;
            box-shadow: none;
            transition: border-color 150ms ease, box-shadow 150ms ease;
        }

        .form-control::placeholder {
            color: #8A9690;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 0.22rem rgba(11, 107, 79, 0.12);
        }

        .input-group-text {
            border: 1px solid var(--line);
            background: var(--paper);
            color: var(--muted);
            font-family: var(--font-mono);
            font-size: 0.8rem;
            font-weight: 500;
        }

        .form-text {
            margin-top: 0.45rem;
            color: #6B7872;
            font-size: 0.76rem;
            line-height: 1.45;
        }

        .basico-preview {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem 1rem;
            border: 1px dashed var(--line);
            border-radius: 12px;
            background: #F5F8F6;
        }

        .preview-label {
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .preview-value {
            color: var(--brand-dark);
            font-family: var(--font-mono);
            font-size: 0.95rem;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
            text-align: right;
        }

        .submit-btn {
            padding: 0.9rem 1rem;
            border: 1px solid var(--brand);
            border-radius: 12px;
            background: var(--brand);
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            box-shadow: 0 14px 24px -16px rgba(11, 107, 79, 0.9);
            transition: transform 150ms ease, background-color 150ms ease, box-shadow 150ms ease;
        }

        .submit-btn:hover {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 18px 28px -16px rgba(11, 107, 79, 0.9);
        }

        .submit-btn:active {
            transform: translateY(0);
            box-shadow: 0 10px 18px -16px rgba(11, 107, 79, 0.9);
        }

        .submit-btn:focus-visible,
        .form-control:focus-visible,
        .form-select:focus-visible {
            outline: 3px solid rgba(11, 107, 79, 0.35);
            outline-offset: 3px;
        }

        .form-footer {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 0.75rem;
            margin-top: 1.6rem;
            padding-top: 1rem;
            border-top: 1px solid var(--line);
            color: #6B7872;
            font-size: 0.72rem;
            line-height: 1.4;
        }

        @media (max-width: 575.98px) {
            .liquidacion-card {
                border-radius: 18px;
            }

            .display-title {
                font-size: 2rem;
            }

            .form-footer {
                flex-direction: column;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .liquidacion-card {
                animation: none;
            }

            .submit-btn {
                transition: none;
            }
        }
    </style>
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

    <!--
        FASE 1 · Mejora progresiva.
        El formulario funciona sin JavaScript; este bloque solo muestra el básico.
    -->
    <script>
        (() => {
            const basicos = {
                A: 4050.55,
                B: 3209.20,
                C: 2575.73,
                D: 1812.28
            };

            const selectCategoria = document.getElementById("categoria");
            const salidaBasico = document.getElementById("basicoPreview");

            const formatearPesos = (valor) => {
                return "$" + valor.toLocaleString("es-AR", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            };

            const actualizarPreview = () => {
                const valor = basicos[selectCategoria.value];
                salidaBasico.textContent = valor
                    ? formatearPesos(valor)
                    : "Elegí una categoría";
            };

            selectCategoria.addEventListener("change", actualizarPreview);
            actualizarPreview();
        })();
    </script>
</body>
</html>
