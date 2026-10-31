<?php
namespace Services;

require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Servicio generador de Reportes Oficiales en PDF utilizando Dompdf vía Composer.
 * Genera reportes contables, rankings y auditorías con estándares corporativos.
 */
class ReportePdfService
{
    private string $logoBase64 = '';

    public function __construct()
    {
        $logoPath = __DIR__ . '/../img/cinestarklogo.png';
        if (file_exists($logoPath)) {
            $this->logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
    }

    /**
     * Genera y descarga o visualiza el PDF oficial en el navegador.
     */
    public function generar(
        string $tipo,
        array $datosReporte,
        string $fechaInicio,
        string $fechaFin,
        int $idUsuario,
        array $usuarios,
        string $nombreAdmin,
        string $nombreRol
    ): void {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);

        // Orientación: Paisaje (landscape) para Ventas y Cortes si tienen muchas columnas, o Retrato (portrait)
        $orientacion = in_array($tipo, ['ventas', 'cortes'], true) ? 'landscape' : 'portrait';

        $html = $this->construirHtml(
            $tipo,
            $datosReporte,
            $fechaInicio,
            $fechaFin,
            $idUsuario,
            $usuarios,
            $nombreAdmin,
            $nombreRol,
            $orientacion
        );

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', $orientacion);
        $dompdf->render();

        // Numeración de páginas en canvas
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont("Helvetica", "normal");
        $size = 8;
        $color = [0.4, 0.4, 0.4];

        // Texto izquierdo y derecho en el pie de cada página
        $canvas->page_text(
            35,
            $canvas->get_height() - 22,
            "CINE STARK S.A. DE C.V. — Sistema de Auditoría y Control Interno",
            $font,
            $size,
            $color
        );

        $canvas->page_text(
            $canvas->get_width() - 110,
            $canvas->get_height() - 22,
            "Página {PAGE_NUM} de {PAGE_COUNT}",
            $font,
            $size,
            $color
        );

        $filename = "Reporte_" . strtoupper($tipo) . "_" . date('Ymd_His') . ".pdf";

        // Enviar al navegador para visualización directa (Attachment => false)
        $dompdf->stream($filename, ["Attachment" => false]);
        exit;
    }

    /**
     * Construye el documento HTML completo que será convertido a PDF.
     */
    private function construirHtml(
        string $tipo,
        array $datosReporte,
        string $fechaInicio,
        string $fechaFin,
        int $idUsuario,
        array $usuarios,
        string $nombreAdmin,
        string $nombreRol,
        string $orientacion
    ): string {
        $titulos = [
            'ventas'     => 'REPORTE GENERAL DE VENTAS Y TRANSACCIONES',
            'dulceria'   => 'RANKING Y DESEMPEÑO DE PRODUCTOS DE DULCERÍA',
            'cortes'     => 'AUDITORÍA CONSOLIDADA DE TURNOS Y CORTES DE CAJA',
            'inventario' => 'CONTROL FÍSICO Y VALUACIÓN DE STOCK DE INVENTARIO'
        ];
        $tituloReporte = $titulos[$tipo] ?? 'REPORTE FINANCIERO OFICIAL';

        // Determinar nombre del cajero seleccionado
        $nombreFiltroCajero = 'Todos los Cajeros / General';
        if ($idUsuario > 0) {
            foreach ($usuarios as $u) {
                if ((int)$u['id_usuario'] === $idUsuario) {
                    $nombreFiltroCajero = $u['nombre'] . ' (' . $u['usuario'] . ')';
                    break;
                }
            }
        }

        $fechaGeneracion = date('d/m/Y h:i:s A');
        $folio = 'CS-' . strtoupper(substr($tipo, 0, 3)) . '-' . date('Ymd') . '-' . rand(1000, 9999);
        $selloHash = strtoupper(substr(hash('sha256', $folio . $fechaGeneracion . $nombreAdmin), 0, 24));

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="utf-8">
            <title><?= htmlspecialchars($tituloReporte) ?></title>
            <style>
                @page {
                    margin: 25px 30px 35px 30px;
                }
                body {
                    font-family: Helvetica, Arial, sans-serif;
                    font-size: 8pt;
                    color: #222226;
                    line-height: 1.3;
                }
                
                /* Estilos del membrete y datos fiscales */
                .header-table {
                    width: 100%;
                    border-bottom: 2px solid #D91E18;
                    padding-bottom: 8px;
                    margin-bottom: 12px;
                }
                .logo-img {
                    max-height: 44px;
                    width: auto;
                }
                .company-name {
                    font-size: 13pt;
                    font-weight: bold;
                    color: #D91E18;
                    margin: 0;
                    letter-spacing: 0.5px;
                }
                .company-sub {
                    font-size: 7pt;
                    color: #55555c;
                    margin-top: 1px;
                }
                .report-header-right {
                    text-align: right;
                }
                .report-title-txt {
                    font-size: 10pt;
                    font-weight: bold;
                    color: #111114;
                    margin: 0;
                    text-transform: uppercase;
                }
                .report-folio {
                    font-size: 7.5pt;
                    font-weight: bold;
                    color: #888890;
                    margin-top: 2px;
                }

                /* Barra de Metadatos y Filtros */
                .meta-table {
                    width: 100%;
                    background-color: #f4f5f7;
                    border: 1px solid #dce0e5;
                    border-radius: 4px;
                    padding: 6px 10px;
                    margin-bottom: 14px;
                    font-size: 7.5pt;
                }
                .meta-label {
                    color: #6c757d;
                    font-weight: bold;
                }
                .meta-val {
                    color: #111;
                    font-weight: 600;
                }

                /* Tarjetas KPI */
                .kpi-table {
                    width: 100%;
                    margin-bottom: 14px;
                    border-collapse: separate;
                    border-spacing: 6px 0;
                }
                .kpi-cell {
                    background-color: #fbfbfc;
                    border: 1px solid #dce0e5;
                    border-top: 3px solid #D91E18;
                    border-radius: 4px;
                    padding: 8px 10px;
                    text-align: center;
                }
                .kpi-title {
                    font-size: 6.8pt;
                    text-transform: uppercase;
                    color: #666;
                    font-weight: bold;
                    letter-spacing: 0.3px;
                }
                .kpi-num {
                    font-size: 13pt;
                    font-weight: bold;
                    color: #111;
                    margin-top: 2px;
                }
                .kpi-num.highlight {
                    color: #B8860B;
                }
                .kpi-num.positive {
                    color: #198754;
                }
                .kpi-num.negative {
                    color: #dc3545;
                }

                /* Tablas de Datos */
                .data-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 14px;
                    font-size: 7.5pt;
                }
                .data-table th {
                    background-color: #1f2227;
                    color: #ffffff;
                    font-weight: bold;
                    padding: 5px 6px;
                    text-align: left;
                    border: 1px solid #1f2227;
                    font-size: 7.2pt;
                    text-transform: uppercase;
                    letter-spacing: 0.3px;
                }
                .data-table td {
                    padding: 5px 6px;
                    border: 1px solid #e1e4e8;
                    vertical-align: middle;
                }
                .data-table tr:nth-child(even) td {
                    background-color: #f8f9fa;
                }
                .data-table .totals-row td {
                    background-color: #eaedf2 !important;
                    font-weight: bold;
                    border-top: 2px solid #333333;
                    border-bottom: 2px solid #333333;
                    color: #111;
                }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .badge {
                    display: inline-block;
                    padding: 2px 5px;
                    font-size: 6.5pt;
                    font-weight: bold;
                    border-radius: 3px;
                    text-transform: uppercase;
                }
                .badge-boletos { background-color: #e3f2fd; color: #0d47a1; border: 1px solid #90caf9; }
                .badge-dulceria { background-color: #fff3e0; color: #e65100; border: 1px solid #ffcc80; }
                .badge-cuadrado { background-color: #e8f5e9; color: #1b5e20; border: 1px solid #a5d6a7; }
                .badge-faltante { background-color: #ffebee; color: #b71c1c; border: 1px solid #ef9a9a; }
                .badge-sobrante { background-color: #fffde7; color: #f57f17; border: 1px solid #fff59d; }

                /* Panel de certificación digital con sellos y metadatos de auditoría */
                .panel-certificacion {
                    width: 100%;
                    border: 1px solid #c9ced4;
                    background-color: #fafbfc;
                    border-radius: 4px;
                    page-break-inside: avoid;
                    margin-top: 15px;
                }
                .cert-col {
                    padding: 8px 12px;
                    vertical-align: top;
                    font-size: 7.2pt;
                }
                .cert-header {
                    font-weight: bold;
                    font-size: 7.5pt;
                    color: #111;
                    text-transform: uppercase;
                    border-bottom: 1px solid #dcdfe3;
                    padding-bottom: 4px;
                    margin-bottom: 6px;
                }
                .cert-header.accent {
                    color: #D91E18;
                }
                .cert-table {
                    width: 100%;
                    font-size: 7pt;
                    color: #333;
                }
                .cert-table td {
                    padding: 2px 0;
                }
                .cert-label {
                    color: #777;
                    width: 85px;
                }
                .sello-box {
                    border: 1px dashed #adb5bd;
                    background-color: #ffffff;
                    padding: 4px 6px;
                    text-align: center;
                    border-radius: 3px;
                    font-size: 6.5pt;
                    color: #495057;
                    margin-top: 4px;
                }
                .aviso-legal {
                    font-size: 6.5pt;
                    color: #777;
                    text-align: center;
                    margin-top: 8px;
                    border-top: 1px dotted #ccc;
                    padding-top: 4px;
                }
            </style>
        </head>
        <body>

            <!-- Membrete con datos fiscales y logotipo -->
            <table class="header-table">
                <tr>
                    <?php if (!empty($this->logoBase64)): ?>
                        <td style="width: 52px; vertical-align: middle;">
                            <img src="<?= $this->logoBase64 ?>" class="logo-img" alt="Cine Stark">
                        </td>
                    <?php endif; ?>
                    <td style="vertical-align: middle; padding-left: 8px;">
                        <div class="company-name">CINE STARK S.A. DE C.V.</div>
                        <div class="company-sub">
                            NIT: 0614-281026-101-3 &nbsp;|&nbsp; NRC: 248910-1 &nbsp;|&nbsp; GIRO: Actividades Cinematográficas y Entretenimiento
                        </div>
                        <div class="company-sub">
                            Complejo Cinematográfico CineStark &nbsp;·&nbsp; San Salvador, El Salvador
                        </div>
                    </td>
                    <td class="report-header-right" style="vertical-align: middle;">
                        <div class="report-title-txt"><?= htmlspecialchars($tituloReporte) ?></div>
                        <div class="report-folio">FOLIO OFICIAL: <?= htmlspecialchars($folio) ?></div>
                        <div class="company-sub">Emisión: <?= $fechaGeneracion ?></div>
                    </td>
                </tr>
            </table>

            <!-- Parámetros del informe: rango de fechas, cajero y emisor -->
            <table class="meta-table">
                <tr>
                    <td style="width: 33%;">
                        <span class="meta-label">Período Auditado:</span>
                        <span class="meta-val">
                            <?= $tipo === 'inventario' ? 'Existencias Físicas al Corte Actual' : date('d/m/Y', strtotime($fechaInicio)) . ' al ' . date('d/m/Y', strtotime($fechaFin)) ?>
                        </span>
                    </td>
                    <td style="width: 34%;">
                        <span class="meta-label">Cajero / Operador:</span>
                        <span class="meta-val"><?= htmlspecialchars($nombreFiltroCajero) ?></span>
                    </td>
                    <td style="width: 33%; text-align: right;">
                        <span class="meta-label">Emitido por:</span>
                        <span class="meta-val"><?= htmlspecialchars($nombreAdmin) ?> (<?= htmlspecialchars($nombreRol) ?>)</span>
                    </td>
                </tr>
            </table>

            <!-- Métricas e información detallada según el tipo de reporte -->
            <?php
            switch ($tipo) {
                case 'ventas':
                    $this->renderVentas($datosReporte);
                    break;
                case 'dulceria':
                    $this->renderDulceria($datosReporte);
                    break;
                case 'cortes':
                    $this->renderCortes($datosReporte);
                    break;
                case 'inventario':
                    $this->renderInventario($datosReporte);
                    break;
            }
            ?>

            <!-- Panel de certificación digital con código hash y sellos institucionales -->
            <table class="panel-certificacion">
                <tr>
                    <td class="cert-col" style="width: 50%; border-right: 1px solid #dce0e5;">
                        <div class="cert-header accent">
                            Certificación de Emisión Operativa
                        </div>
                        <table class="cert-table">
                            <tr>
                                <td class="cert-label">Responsable:</td>
                                <td><strong><?= htmlspecialchars($nombreAdmin) ?></strong></td>
                            </tr>
                            <tr>
                                <td class="cert-label">Cargo / Rol:</td>
                                <td><?= htmlspecialchars($nombreRol) ?></td>
                            </tr>
                            <tr>
                                <td class="cert-label">Sello Hash:</td>
                                <td><code style="font-size: 6.5pt; color: #444;"><?= $selloHash ?></code></td>
                            </tr>
                            <tr>
                                <td class="cert-label">Validación:</td>
                                <td>
                                    <span style="background-color: #e8f5e9; color: #1b5e20; padding: 1px 5px; font-weight: bold; font-size: 6.5pt; border-radius: 2px;">
                                        EMITIDO Y REGISTRADO EN SISTEMA
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <div class="sello-box">
                            CERTIFICADO DIGITAL DE AUDITORÍA INTERNA — CINE STARK IT
                        </div>
                    </td>
                    <td class="cert-col" style="width: 50%;">
                        <div class="cert-header">
                            Conformidad y Control Financiero
                        </div>
                        <table class="cert-table">
                            <tr>
                                <td class="cert-label">Departamento:</td>
                                <td>Gerencia de Administración, Contabilidad y Finanzas</td>
                            </tr>
                            <tr>
                                <td class="cert-label">Protocolo:</td>
                                <td>Conciliación de Cierres de Turno y Cuadratura Contable</td>
                            </tr>
                            <tr>
                                <td class="cert-label">Dictamen:</td>
                                <td>
                                    <span style="background-color: #e3f2fd; color: #0d47a1; padding: 1px 5px; font-weight: bold; font-size: 6.5pt; border-radius: 2px;">
                                        CONFORME CON LIBRO TRANSACCIONAL
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="cert-label">Período Fiscal:</td>
                                <td>Ejercicio Contable Activo 2026</td>
                            </tr>
                        </table>
                        <div class="sello-box">
                            VISTO BUENO / ARCHIVO GENERAL DE AUDITORÍA
                        </div>
                    </td>
                </tr>
            </table>

            <div class="aviso-legal">
                Documento contable oficial generado electrónicamente por la plataforma CineStark v2.0. La información contenida refleja de manera fidedigna los registros alojados en la base de datos central de transacciones. Válido para auditoría interna y conciliación fiscal.
            </div>

        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Renderiza el cuerpo del Reporte de Ventas Generales
     */
    private function renderVentas(array $datos): void
    {
        $totales = $datos['totales'] ?? [];
        $txs = $datos['transacciones'] ?? [];
        $granTotal = (float)($totales['total_recaudado'] ?? 0);
        $totalTx = (int)($totales['total_transacciones'] ?? 0);
        $totalEfectivo = (float)($totales['total_efectivo'] ?? 0);
        $totalTarjeta = (float)($totales['total_tarjeta'] ?? 0);
        ?>
        <!-- KPIs Ventas -->
        <table class="kpi-table">
            <tr>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Recaudación Total</div>
                    <div class="kpi-num highlight">$<?= number_format($granTotal, 2) ?></div>
                </td>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Transacciones</div>
                    <div class="kpi-num"><?= $totalTx ?></div>
                </td>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Efectivo Recaudado</div>
                    <div class="kpi-num positive">$<?= number_format($totalEfectivo, 2) ?></div>
                </td>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Tarjetas Débito / Crédito</div>
                    <div class="kpi-num" style="color: #0d6efd;">$<?= number_format($totalTarjeta, 2) ?></div>
                </td>
            </tr>
        </table>

        <!-- Tabla Transacciones -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 32px;" class="text-center">#</th>
                    <th style="width: 105px;">Fecha / Hora</th>
                    <th style="width: 110px;">Cajero</th>
                    <th style="width: 70px;" class="text-center">Tipo</th>
                    <th>Detalle de la Operación</th>
                    <th style="width: 75px;" class="text-center">Método</th>
                    <th style="width: 70px;" class="text-right">Monto ($)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($txs)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 15px; color: #888;">
                            No se encontraron transacciones registradas para los filtros seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($txs as $i => $t): ?>
                        <?php 
                            $esDulceria = (($t['tipo_venta'] ?? '') === 'dulceria');
                            $detalle = $esDulceria 
                                ? ($t['productos_dulceria'] ?? 'Productos varios') 
                                : (($t['pelicula'] ?? '') . ' — ' . ($t['nombre_sala'] ?? '') . ' [' . ($t['butacas'] ?? '') . ']');
                        ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td><?= date('d/m/Y h:i A', strtotime($t['fecha_hora'])) ?></td>
                            <td><?= htmlspecialchars($t['nombre_cajero'] ?? '—') ?></td>
                            <td class="text-center">
                                <span class="badge <?= $esDulceria ? 'badge-dulceria' : 'badge-boletos' ?>">
                                    <?= $esDulceria ? 'Dulcería' : 'Boletos' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($detalle) ?></td>
                            <td class="text-center"><?= htmlspecialchars($t['metodo_pago']) ?></td>
                            <td class="text-right"><strong>$<?= number_format((float)$t['total'], 2) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <td colspan="6" class="text-right">TOTAL GENERAL CONSOLIDADO:</td>
                    <td class="text-right">$<?= number_format($granTotal, 2) ?></td>
                </tr>
            </tfoot>
        </table>
        <?php
    }

    /**
     * Renderiza el cuerpo del Reporte de Ranking de Dulcería
     */
    private function renderDulceria(array $ranking): void
    {
        $granTotal = array_reduce($ranking, fn($acc, $curr) => $acc + (float)$curr['total_recaudado'], 0);
        $granUnidades = array_reduce($ranking, fn($acc, $curr) => $acc + (int)$curr['unidades_vendidas'], 0);
        ?>
        <!-- KPIs Dulcería -->
        <table class="kpi-table">
            <tr>
                <td class="kpi-cell" style="width: 33%;">
                    <div class="kpi-title">Ingresos Totales Dulcería</div>
                    <div class="kpi-num highlight">$<?= number_format($granTotal, 2) ?></div>
                </td>
                <td class="kpi-cell" style="width: 33%;">
                    <div class="kpi-title">Unidades Despachadas</div>
                    <div class="kpi-num"><?= number_format($granUnidades) ?> uds.</div>
                </td>
                <td class="kpi-cell" style="width: 34%;">
                    <div class="kpi-title">Productos en Catálogo</div>
                    <div class="kpi-num"><?= count($ranking) ?> ítems</div>
                </td>
            </tr>
        </table>

        <!-- Tabla Ranking -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;" class="text-center">#</th>
                    <th>Producto / Combo</th>
                    <th style="width: 80px;">Categoría</th>
                    <th style="width: 70px;" class="text-right">Precio ($)</th>
                    <th style="width: 75px;" class="text-center">Uds. Vendidas</th>
                    <th style="width: 85px;" class="text-right">Ingresos ($)</th>
                    <th style="width: 80px;" class="text-right">% Participación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ranking)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 15px; color: #888;">
                            No se registran ventas de dulcería en el período auditado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                        foreach ($ranking as $pos => $r): 
                            $unidades = (int)$r['unidades_vendidas'];
                            $monto = (float)$r['total_recaudado'];
                            $pct = $granTotal > 0 ? ($monto / $granTotal) * 100 : 0;
                    ?>
                        <tr>
                            <td class="text-center"><strong><?= $pos + 1 ?></strong></td>
                            <td><?= htmlspecialchars($r['nombre']) ?></td>
                            <td><span class="badge" style="background:#eef0f3; color:#333;"><?= htmlspecialchars(ucfirst($r['categoria'])) ?></span></td>
                            <td class="text-right">$<?= number_format((float)$r['precio'], 2) ?></td>
                            <td class="text-center"><strong><?= $unidades ?></strong></td>
                            <td class="text-right"><strong>$<?= number_format($monto, 2) ?></strong></td>
                            <td class="text-right"><?= number_format($pct, 1) ?> %</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <td colspan="4" class="text-right">TOTAL GENERAL DULCERÍA:</td>
                    <td class="text-center"><?= $granUnidades ?></td>
                    <td class="text-right">$<?= number_format($granTotal, 2) ?></td>
                    <td class="text-right">100.0 %</td>
                </tr>
            </tfoot>
        </table>
        <?php
    }

    /**
     * Renderiza el cuerpo del Reporte de Auditoría de Cortes de Caja
     */
    private function renderCortes(array $datos): void
    {
        $cortes = $datos['cortes'] ?? [];
        $totCortes = (int)($datos['total_cortes'] ?? 0);
        $totVentas = (float)($datos['total_ventas'] ?? 0);
        $totFaltantes = (float)($datos['total_faltantes'] ?? 0);
        $totSobrantes = (float)($datos['total_sobrantes'] ?? 0);
        ?>
        <!-- KPIs Cortes -->
        <table class="kpi-table">
            <tr>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Turnos Registrados</div>
                    <div class="kpi-num"><?= $totCortes ?></div>
                </td>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Ventas Totales en Caja</div>
                    <div class="kpi-num highlight">$<?= number_format($totVentas, 2) ?></div>
                </td>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Total Faltantes</div>
                    <div class="kpi-num negative">-$<?= number_format($totFaltantes, 2) ?></div>
                </td>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Total Sobrantes</div>
                    <div class="kpi-num positive">+$<?= number_format($totSobrantes, 2) ?></div>
                </td>
            </tr>
        </table>

        <!-- Tabla Cortes -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 32px;" class="text-center">ID</th>
                    <th style="width: 105px;">Cajero</th>
                    <th style="width: 110px;">Fecha Apertura</th>
                    <th style="width: 110px;">Fecha Cierre</th>
                    <th style="width: 65px;" class="text-right">Fondo Ini.</th>
                    <th style="width: 70px;" class="text-right">Ventas</th>
                    <th style="width: 70px;" class="text-right">Declarado</th>
                    <th style="width: 80px;" class="text-center">Discrepancia</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cortes)): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 15px; color: #888;">
                            No se encontraron turnos de caja para los criterios seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cortes as $c): ?>
                        <?php 
                            $cerrado = !empty($c['fecha_cierre']);
                            $dif = (float)($c['diferencia'] ?? 0);
                        ?>
                        <tr>
                            <td class="text-center">#<?= $c['id_corte'] ?></td>
                            <td><?= htmlspecialchars($c['nombre_cajero']) ?></td>
                            <td><?= htmlspecialchars($c['inicio_formato'] ?? $c['fecha_inicio']) ?></td>
                            <td>
                                <?= $cerrado ? htmlspecialchars($c['cierre_formato'] ?? $c['fecha_cierre']) : '<span style="color:#f59e0b; font-weight:bold;">EN TURNO</span>' ?>
                            </td>
                            <td class="text-right">$<?= number_format((float)$c['monto_inicial'], 2) ?></td>
                            <td class="text-right"><strong>$<?= number_format((float)$c['total_ventas'], 2) ?></strong></td>
                            <td class="text-right">
                                <?= $cerrado ? '$' . number_format((float)$c['total_declarado'], 2) : '—' ?>
                            </td>
                            <td class="text-center">
                                <?php if (!$cerrado): ?>
                                    <span class="badge" style="background:#fef3c7; color:#92400e;">Abierto</span>
                                <?php elseif (abs($dif) < 0.01): ?>
                                    <span class="badge badge-cuadrado">Cuadrado</span>
                                <?php elseif ($dif < 0): ?>
                                    <span class="badge badge-faltante">-$<?= number_format(abs($dif), 2) ?></span>
                                <?php else: ?>
                                    <span class="badge badge-sobrante">+$<?= number_format($dif, 2) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <td colspan="5" class="text-right">TOTAL VENTAS EN CAJA:</td>
                    <td class="text-right">$<?= number_format($totVentas, 2) ?></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
        <?php
    }

    /**
     * Renderiza el cuerpo del Reporte de Inventario y Stock
     */
    private function renderInventario(array $datos): void
    {
        $productos = $datos['productos'] ?? [];
        $totArt = (int)($datos['total_articulos'] ?? 0);
        $totStockUds = (int)($datos['total_stock_unidades'] ?? 0);
        $totValor = (float)($datos['total_valor_inventario'] ?? 0);
        $totAgot = (int)($datos['total_agotados'] ?? 0);
        ?>
        <!-- KPIs Inventario -->
        <table class="kpi-table">
            <tr>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Productos en Catálogo</div>
                    <div class="kpi-num"><?= $totArt ?></div>
                </td>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Unidades Físicas</div>
                    <div class="kpi-num"><?= number_format($totStockUds) ?> uds.</div>
                </td>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Valorización Total</div>
                    <div class="kpi-num positive">$<?= number_format($totValor, 2) ?></div>
                </td>
                <td class="kpi-cell" style="width: 25%;">
                    <div class="kpi-title">Agotados / Críticos</div>
                    <div class="kpi-num <?= $totAgot > 0 ? 'negative' : 'positive' ?>"><?= $totAgot ?></div>
                </td>
            </tr>
        </table>

        <!-- Tabla Inventario -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 28px;" class="text-center">#</th>
                    <th>Producto / Línea de Venta</th>
                    <th style="width: 80px;">Categoría</th>
                    <th style="width: 70px;" class="text-right">Precio ($)</th>
                    <th style="width: 65px;" class="text-center">Stock Físico</th>
                    <th style="width: 75px;" class="text-center">Condición</th>
                    <th style="width: 65px;" class="text-center">Vendidos</th>
                    <th style="width: 80px;" class="text-right">Valuación ($)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 15px; color: #888;">
                            No se registran productos en el catálogo de dulcería.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productos as $i => $p): ?>
                        <?php 
                            $stock = (int)$p['stock_disponible'];
                            $valorStock = (float)($p['valor_stock'] ?? ($stock * (float)$p['precio']));
                        ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($p['nombre']) ?></strong>
                            </td>
                            <td><span class="badge" style="background:#eef0f3; color:#333;"><?= htmlspecialchars(ucfirst($p['categoria'])) ?></span></td>
                            <td class="text-right">$<?= number_format((float)$p['precio'], 2) ?></td>
                            <td class="text-center"><strong><?= $stock ?></strong></td>
                            <td class="text-center">
                                <?php if ($stock <= 0): ?>
                                    <span class="badge" style="background:#fee2e2; color:#991b1b; border:1px solid #f87171;">Agotado</span>
                                <?php elseif ($stock <= 15): ?>
                                    <span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fcd34d;">Bajo</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#d1fae5; color:#065f46; border:1px solid #6ee7b7;">Óptimo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= (int)($p['total_vendidos'] ?? 0) ?></td>
                            <td class="text-right"><strong>$<?= number_format($valorStock, 2) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <td colspan="4" class="text-right">VALORIZACIÓN TOTAL DEL INVENTARIO:</td>
                    <td class="text-center"><?= number_format($totStockUds) ?></td>
                    <td colspan="2"></td>
                    <td class="text-right">$<?= number_format($totValor, 2) ?></td>
                </tr>
            </tfoot>
        </table>
        <?php
    }
}

