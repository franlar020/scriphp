# TP Programación 4 — Liquidación de sueldos

Aplicación web en **PHP 8.2** para calcular el neto de un empleado a partir de su **categoría** (A–D) y sus **años de antigüedad**. El sistema valida los datos en el servidor, calcula haberes y retenciones con una política de redondeo explícita y presenta un comprobante imprimible.

> Trabajo Práctico de **Programación 4 — UNLAR, Ingeniería en Sistemas**.

## Resumen

- Formulario HTML con envío `POST` a `procesar.php`.
- Backend PHP puro: sin framework, sin Composer, sin base de datos y sin build step.
- Validación en el servidor: categoría `A|B|C|D` y antigüedad entera entre `1` y `30`.
- Cálculo de básico, bonificaciones, retenciones y neto.
- Vista de comprobante y vista de error en un mismo archivo `resultado.view.php`.
- CSS y JavaScript separados en `assets/`, sin bloques inline en los PHP.
- Interfaz responsive con Bootstrap 5 por CDN y tipografías de Google Fonts.
- Caso de aceptación verificado: **Categoría A + 9 años → neto `$8.676,27`**.

## Estructura del proyecto

```text
scriphp/
├── index.php                 # FASE 1 — formulario HTML y enlaces a assets
├── procesar.php              # FASES 2–4 — validación, cálculo y delegación a la vista
├── resultado.view.php        # FASE 4 — comprobante y error, solo accesible desde procesar.php
├── assets/
│   ├── css/
│   │   └── styles.css        # estilos únicos del proyecto
│   └── js/
│       └── app.js            # ES6: vista previa del básico e impresión
├── PROMPT.txt                # prompt utilizado en el desarrollo asistido
└── README.md                 # documentación del proyecto
```

### Responsabilidades por fase

| Fase | Responsable | Archivo principal | Qué hace |
| --- | --- | --- | --- |
| FASE 1 | Francisco | `index.php` + `assets/` | Formulario, Bootstrap 5, tipografías, CSS/JS externos y vista previa del básico. |
| FASE 2 | Bruno | `procesar.php` | Recepción `POST`, validación de categoría/antigüedad y sueldo básico con `match`. |
| FASE 3 | Camila | `procesar.php` | Bonificaciones por antigüedad, presentismo y dedicación especial; total de haberes. |
| FASE 4 | Virginia | `procesar.php` + `resultado.view.php` | Retenciones, total neto, comprobante, error e impresión. |

### Flujo de datos

```text
index.php  --POST-->  procesar.php  --require_once-->  resultado.view.php
                      valida y calcula                 comprobante o error
```

- `index.php` no contiene lógica de negocio.
- `procesar.php` no contiene HTML.
- `resultado.view.php` no calcula: solo muestra datos ya calculados.
- `resultado.view.php` tiene un guard con la constante `LIQUIDACION_APP`; un acceso directo por URL responde **404**.

## Requisitos

- **XAMPP** con Apache y **PHP 8.2**.
- Navegador moderno.
- Conexión a internet para Bootstrap 5 por CDN y Google Fonts.  
  El cálculo PHP funciona sin internet; solo cambia el aspecto visual si los CDN no cargan.

## Instalación y ejecución

1. Clonar el repositorio dentro de la carpeta `htdocs` de XAMPP:

   ```bash
   git clone https://github.com/franlar020/scriphp.git C:\xampp\htdocs\scriphp
   ```

   Si ya tenés el proyecto, copiá la carpeta del proyecto como `scriphp` dentro de `htdocs`.

2. Iniciar **Apache** desde el panel de control de XAMPP.

3. Abrir la aplicación:

   ```text
   http://localhost/scriphp/index.php
   ```

4. Elegir una categoría, ingresar la antigüedad y pulsar **Calcular Liquidación**.

### Verificación rápida de sintaxis

En Windows, con `php` no agregado al PATH:

```powershell
C:\xampp\php\php.exe -l index.php
C:\xampp\php\php.exe -l procesar.php
C:\xampp\php\php.exe -l resultado.view.php
```

En Linux/macOS, si `php` está en el PATH:

```bash
php -l index.php
php -l procesar.php
php -l resultado.view.php
```

## Reglas de negocio

### Sueldo básico

| Categoría | Sueldo básico |
| --- | ---: |
| A | `$4.050,55` |
| B | `$3.209,20` |
| C | `$2.575,73` |
| D | `$1.812,28` |

El básico se asigna con `match` en `procesar.php`.

### Bonificaciones

Sobre el sueldo básico:

| Concepto | Fórmula |
| --- | --- |
| Antigüedad | `básico × 0.10 × años` |
| Presentismo | `básico × 0.15` |
| Dedicación especial | `básico × 0.50` |

La antigüedad es literalmente **10 % por año**: 30 años equivalen al **300 %** del básico.

### Retenciones

Se calculan sobre el **total de haberes**, no sobre el básico:

| Concepto | Porcentaje |
| --- | ---: |
| Obra social | `5 %` |
| ANSES / Jubilación | `11 %` |

### Redondeo

La política de redondeo es intencional y forma parte de la consigna:

1. El total de haberes se redondea **una sola vez** a 2 decimales.
2. Cada retención se redondea a 2 decimales **antes** de sumarlas.
3. El total neto se obtiene restando los descuentos ya redondeados.

Por eso, si se restan valores sin redondear, el caso A/9 da `$8.676,28` y **no** es el resultado esperado.

### Formato de importes

Todos los montos se muestran con:

```php
number_format($valor, 2, ',', '.')
```

y el prefijo `$` **sin espacio**, por ejemplo `$8.676,27`.

## Caso de aceptación

**Categoría A + 9 años de antigüedad**

| Concepto | Importe |
| --- | ---: |
| Sueldo básico | `$4.050,55` |
| Bonificación por antigüedad | `$3.645,50` |
| Bonificación por presentismo | `$607,58` |
| Bonificación por dedicación especial | `$2.025,28` |
| **Total haberes** | **`$10.328,90`** |
| Obra social | `-$516,45` |
| ANSES | `-$1.136,18` |
| **Total descuentos** | **`-$1.652,63`** |
| **Total neto** | **`$8.676,27`** |

### Prueba con `curl`

```bash
curl -s -X POST -d "categoria=A&antiguedad=9" \
  http://localhost/scriphp/procesar.php | grep -o '8\.676,27'
```

Resultado esperado:

```text
8.676,27
```

## Comportamiento HTTP esperado

| Caso | Resultado |
| --- | --- |
| `GET` directo a `procesar.php` | `405` |
| `POST` con categoría inválida o antigüedad fuera de `1–30` | `400` |
| `GET` directo a `resultado.view.php` | `404` |
| `GET` a `assets/css/styles.css` | `200` |
| `GET` a `assets/js/app.js` | `200` |
| `POST` válido | Comprobante con `200` |

Comandos de control:

```bash
curl -s -o NUL -w '%{http_code}\n' http://localhost/scriphp/procesar.php
curl -s -o NUL -w '%{http_code}\n' -X POST -d 'categoria=Z&antiguedad=9' http://localhost/scriphp/procesar.php
curl -s -o NUL -w '%{http_code}\n' http://localhost/scriphp/resultado.view.php
curl -s -o NUL -w '%{http_code}\n' http://localhost/scriphp/assets/css/styles.css
```

## Diseño e interfaz

- **Bootstrap 5.3.3** por CDN en `index.php` y `resultado.view.php`.
- Tipografías: **Bricolage Grotesque** para interfaz y **IBM Plex Mono** para importes.
- Paleta centralizada como variables CSS en `assets/css/styles.css`:

```css
--paper: #E9EDF0;
--surface: #FCFDFC;
--ink: #15211D;
--brand: #0B6B4F;
--brand-dark: #07503B;
--brand-soft: #E8F3EE;
--stamp: #B3402A;
--line: #C9D2CE;
--muted: #52605A;
```

- Estética de talón de liquidación: formulario tipo ventanilla y comprobante con sello “LIQUIDADO”.
- Tabla del comprobante con clases `table table-bordered table-striped`.
- Responsive desde `360 px`.
- Foco visible, `prefers-reduced-motion` e impresión limpia sin botones ni decoración.

## JavaScript

`assets/js/app.js` usa ES6 y cumple dos responsabilidades:

1. **Vista previa del básico:** al cambiar la categoría, muestra el sueldo básico en `#basicoPreview` con `Intl.NumberFormat("es-AR", ...)`.
2. **Impresión:** el botón `#printButton` llama a `window.print()`.

El formulario funciona sin JavaScript: la vista previa es una mejora progresiva, no una dependencia.

## Seguridad y accesibilidad

- Validación en el servidor aunque el navegador valide en el cliente.
- Escape de texto dinámico con `e()` basado en `htmlspecialchars`.
- `procesar.php` solo acepta `POST`.
- `resultado.view.php` no puede abrirse directo por URL.
- Uso de `label`, `aria-describedby`, `aria-live` y estructura tabular con `th scope`.
- Los datos del comprobante se muestran una sola vez; no se recalculan en la vista.

## Herramientas de desarrollo

DeepSeek 9B local, Ollama y ngrok se usaron como **contexto de desarrollo/demo**: asistencia con IA, privacidad/offline y prueba en equipo. **No** son dependencias de ejecución del sistema y no hay código de integración en la aplicación.

## Documentación adicional

- `PROMPT.txt`: prompt utilizado en el desarrollo asistido.

## Solución de problemas

| Problema | Causa probable | Acción |
| --- | --- | --- |
| `404` al abrir el proyecto | La carpeta no se llama `scriphp` o Apache no está iniciado. | Revisar `htdocs/scriphp` e iniciar Apache. |
| `405` en la aplicación | Se entró directo a `procesar.php` por `GET`. | Volver a `index.php` y enviar el formulario. |
| `400` al calcular | Categoría fuera de `A–D` o antigüedad fuera de `1–30`. | Corregir los datos y reintentar. |
| `404` en `resultado.view.php` | La vista solo se abre desde `procesar.php`. | Es el comportamiento esperado. |
| No se ve la vista previa del básico | JavaScript está deshabilitado o `app.js` no cargó. | El envío sigue funcionando; revisar consola y ruta de `assets/js/app.js`. |
| El neto difiere en `$0,01` | Se aplicó un redondeo distinto al de la consigna. | Seguir la política de redondeo de este README. |

## Estado del proyecto

Proyecto académico completo: formulario, backend, comprobante, estilos, script y documentación. No incluye base de datos ni tests automatizados; la verificación se realiza con el caso A/9 y los códigos HTTP de la tabla anterior.

## Licencia

Proyecto académico para Programación 4 (UNLAR). No se incluye una licencia de código abierto definida.
