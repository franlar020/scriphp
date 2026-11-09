/* ============================================================
   Liquidación de sueldos · JavaScript del frontend
   FASE 1 · Vista previa del sueldo básico — Francisco
   FASE 4 · Impresión del comprobante — Virginia
   ============================================================ */

(() => {
    "use strict";

    const BASICOS = Object.freeze({
        A: 4050.55,
        B: 3209.20,
        C: 2575.73,
        D: 1812.28
    });

    const formatoPesos = new Intl.NumberFormat("es-AR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    const formatearPesos = (valor) => `$${formatoPesos.format(valor)}`;

    /* FASE 1 · Mejora progresiva: el formulario funciona sin JavaScript. */
    const iniciarVistaPrevia = () => {
        const selectCategoria = document.getElementById("categoria");
        const salidaBasico = document.getElementById("basicoPreview");

        if (!selectCategoria || !salidaBasico) {
            return;
        }

        const actualizarVistaPrevia = () => {
            const valor = BASICOS[selectCategoria.value];
            salidaBasico.textContent = valor
                ? formatearPesos(valor)
                : "Elegí una categoría";
        };

        selectCategoria.addEventListener("change", actualizarVistaPrevia);
        actualizarVistaPrevia();
    };

    /* FASE 4 · Impresión progresiva: el botón solo existe en el comprobante. */
    const iniciarImpresion = () => {
        const botonImprimir = document.getElementById("printButton");

        if (!botonImprimir) {
            return;
        }

        botonImprimir.addEventListener("click", () => {
            window.print();
        });
    };

    const iniciarApp = () => {
        iniciarVistaPrevia();
        iniciarImpresion();
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", iniciarApp, { once: true });
    } else {
        iniciarApp();
    }
})();
