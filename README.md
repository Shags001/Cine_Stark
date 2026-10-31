# CineStark v2.0

Sistema Integral de Gestión Cinematográfica para CineStark S.A. de C.V.

## Módulos del Sistema
- **Boletería:** Selección gráfica de butacas, funciones y cartelera activa.
- **Dulcería (POS):** Catálogo de productos y combos, personalización de sabores y extras, cobro y tickets.
- **Caja y Turnos:** Control estricto de apertura de gaveta, ventas y arqueo/cierre de turnos.
- **Administración:** Gestión de películas, salas, funciones, usuarios, productos y combos.
- **Centro de Reportes:** Auditorías contables, rankings y generación de reportes en PDF con Dompdf / Composer.

## Requisitos de Instalación
- PHP 8.1 o superior
- Servidor Apache / WAMP / XAMPP con extensión `pdo_mysql` y `gd` activas
- Base de datos MySQL
- Composer (para dependencias de `dompdf/dompdf`)

```bash
composer install
```

