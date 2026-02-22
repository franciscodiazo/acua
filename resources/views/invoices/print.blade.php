<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuota Familiar {{ $invoice->numero }}</title>
    <style>
        @page {
            size: letter;
            margin: 0.8cm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            background: #fff;
        }
        .invoice-container {
            width: 100%;
            max-width: 21.59cm;
            height: auto;
            min-height: 26.34cm;
            display: flex;
            flex-direction: column;
        }
        
        /* COLORES INSTITUCIONALES - Verde y Azul del logo */
        :root {
            --color-azul: #1E88E5;
            --color-azul-oscuro: #1565C0;
            --color-azul-claro: #64B5F6;
            --color-verde: #43A047;
            --color-verde-oscuro: #2E7D32;
            --color-verde-claro: #81C784;
        }
        
        /* ENCABEZADO */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: linear-gradient(135deg, var(--color-azul) 0%, var(--color-azul-oscuro) 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-container {
            width: 55px;
            height: 55px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
        }
        .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
        }
        .company-tagline {
            font-size: 9px;
            opacity: 0.9;
        }
        .header-right {
            text-align: right;
        }
        .invoice-label {
            font-size: 10px;
            opacity: 0.9;
        }
        .invoice-number {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .invoice-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .status-pendiente { background: #ffc107; color: #000; }
        .status-pagada { background: var(--color-verde); color: #fff; }
        .status-parcial { background: #fd7e14; color: #fff; }
        .status-anulada { background: #dc3545; color: #fff; }
        
        /* BARRA DE FECHAS */
        .dates-bar {
            display: flex;
            justify-content: space-around;
            background: linear-gradient(135deg, var(--color-verde-claro) 0%, var(--color-azul-claro) 100%);
            padding: 8px;
            border-left: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
        }
        .date-item {
            text-align: center;
        }
        .date-label {
            font-size: 8px;
            color: #fff;
            text-transform: uppercase;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .date-value {
            font-size: 11px;
            font-weight: bold;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        
        /* CONTENIDO PRINCIPAL */
        .main-content {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 10px;
            border-left: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
        }
        
        .section-box {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
        }
        .section-header {
            background: linear-gradient(135deg, var(--color-verde) 0%, var(--color-verde-oscuro) 100%);
            color: white;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 10px;
        }
        .section-header.blue { 
            background: linear-gradient(135deg, var(--color-azul) 0%, var(--color-azul-oscuro) 100%); 
        }
        .section-header.green { 
            background: linear-gradient(135deg, var(--color-verde) 0%, var(--color-verde-oscuro) 100%); 
        }
        .section-header.mixed { 
            background: linear-gradient(135deg, var(--color-verde) 0%, var(--color-azul) 100%); 
        }
        .section-header.red { 
            background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%); 
        }
        
        .section-body {
            padding: 8px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 4px 8px;
        }
        .info-label {
            color: #666;
            font-size: 8px;
        }
        .info-value {
            font-weight: 500;
            font-size: 9px;
        }
        .info-value.highlight {
            color: var(--color-azul);
            font-weight: bold;
        }
        
        /* TABLA DE CONSUMO */
        .consumption-table {
            width: 100%;
            border-collapse: collapse;
        }
        .consumption-table th {
            background: linear-gradient(135deg, var(--color-azul-claro) 0%, var(--color-verde-claro) 100%);
            color: #fff;
            padding: 6px;
            font-size: 8px;
            text-align: center;
            border-bottom: 2px solid var(--color-azul);
        }
        .consumption-table td {
            padding: 6px;
            text-align: center;
            font-size: 9px;
            border-bottom: 1px solid #eee;
        }
        .consumption-table .big-number {
            font-size: 12px;
            font-weight: bold;
            color: var(--color-azul);
        }
        
        /* GRÁFICO DE CONSUMO - 3 BARRAS */
        .chart-section {
            grid-column: span 2;
        }
        .chart-wrapper {
            display: flex;
            gap: 12px;
            align-items: stretch;
        }
        .chart-bars-container {
            flex: 1;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 20px;
            height: 90px;
            padding: 10px;
            background: linear-gradient(to top, rgba(30, 136, 229, 0.1) 0%, #fff 100%);
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }
        .bar-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 65px;
        }
        .bar-value {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            margin-bottom: 4px;
        }
        .bar-wrapper {
            width: 45px;
            height: 60px;
            background: #e9ecef;
            border-radius: 4px 4px 0 0;
            display: flex;
            align-items: flex-end;
            overflow: hidden;
        }
        .bar-fill {
            width: 100%;
            background: linear-gradient(to top, var(--color-azul-oscuro), var(--color-azul-claro));
            border-radius: 5px 5px 0 0;
            transition: height 0.5s ease;
        }
        .bar-fill.current {
            background: linear-gradient(to top, var(--color-verde-oscuro), var(--color-verde-claro));
        }
        .bar-fill.empty {
            background: #ccc;
        }
        .bar-label {
            font-size: 8px;
            color: #666;
            margin-top: 5px;
            text-align: center;
            font-weight: 500;
        }
        
        .chart-stats-box {
            width: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 6px;
        }
        .stat-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 5px;
            padding: 6px;
            text-align: center;
            border-left: 3px solid var(--color-azul);
        }
        .stat-card.green {
            border-left-color: var(--color-verde);
        }
        .stat-card .stat-label {
            font-size: 7px;
            color: #666;
            text-transform: uppercase;
        }
        .stat-card .stat-value {
            font-size: 12px;
            font-weight: bold;
            color: var(--color-azul);
        }
        .stat-card.green .stat-value {
            color: var(--color-verde);
        }
        
        /* TOTALES */
        .totals-section {
            grid-column: span 2;
        }
        .totals-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .total-box {
            text-align: center;
            padding: 10px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 6px;
            border: 2px solid #dee2e6;
        }
        .total-box.highlight {
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.1) 0%, rgba(30, 136, 229, 0.2) 100%);
            border-color: var(--color-azul);
        }
        .total-box.danger {
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            border-color: #dc3545;
        }
        .total-box.success {
            background: linear-gradient(135deg, rgba(67, 160, 71, 0.1) 0%, rgba(67, 160, 71, 0.2) 100%);
            border-color: var(--color-verde);
        }
        .total-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .total-value {
            font-size: 16px;
            font-weight: bold;
        }
        .total-box.highlight .total-value { color: var(--color-azul); }
        .total-box.danger .total-value { color: #dc3545; }
        .total-box.success .total-value { color: var(--color-verde); }
        
        /* CRÉDITOS PENDIENTES */
        .credits-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        .credits-table th {
            background: #ffebee;
            color: #c62828;
            padding: 4px;
            text-align: left;
            font-size: 8px;
        }
        .credits-table td {
            padding: 4px;
            border-bottom: 1px solid #ffcdd2;
        }
        .credits-total {
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            color: #dc3545;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 2px solid #dc3545;
        }
        
        /* PIE DE PÁGINA */
        .footer {
            background: linear-gradient(135deg, var(--color-verde) 0%, var(--color-azul) 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 0 0 8px 8px;
            text-align: center;
        }
        .footer-message {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .footer-info {
            font-size: 8px;
            opacity: 0.9;
            line-height: 1.4;
        }
        .payment-box {
            background: rgba(255,255,255,0.2);
            padding: 6px 12px;
            border-radius: 4px;
            margin: 6px 0;
            display: inline-block;
            font-size: 9px;
        }
        
        /* MARCA DE AGUA */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            color: rgba(0,0,0,0.03);
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
        
        /* BOTÓN IMPRIMIR */
        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, var(--color-verde) 0%, var(--color-verde-oscuro) 100%);
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            box-shadow: 0 3px 10px rgba(67, 160, 71, 0.3);
            z-index: 1000;
        }
        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 160, 71, 0.4);
        }
        
        @media print {
            .print-btn { display: none; }
            body { 
                print-color-adjust: exact; 
                -webkit-print-color-adjust: exact; 
            }
            .invoice-container { 
                height: auto;
                max-height: none;
            }
            @page {
                size: letter;
                margin: 0.8cm;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Imprimir Cuota</button>
    
    @if($invoice->estado === 'anulada')
        <div class="watermark">ANULADA</div>
    @elseif($invoice->estado === 'pagada')
        <div class="watermark">PAGADA</div>
    @endif
    
    <div class="invoice-container">
        <!-- ENCABEZADO -->
        <div class="header">
            <div class="header-left">
                <div class="logo-container">
                    @if($company?->logo)
                        <img src="{{ $company->logo_url }}" alt="Logo">
                    @else
                        <span style="color: #0d6efd; font-size: 35px;">💧</span>
                    @endif
                </div>
                <div>
                    <div class="company-name">{{ $company->nombre ?? 'ACUEDUCTO RURAL' }}</div>
                    <div class="company-tagline">
                        @if($company)
                            NIT: {{ $company->nit }} | {{ $company->municipio }}, {{ $company->departamento }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-label">CUOTA FAMILIAR</div>
                <div class="invoice-number">{{ $invoice->numero }}</div>
                <div class="invoice-status status-{{ $invoice->estado }}">
                    {{ strtoupper($invoice->estado) }}
                </div>
            </div>
        </div>
        
        <!-- BARRA DE FECHAS -->
        <div class="dates-bar">
            <div class="date-item">
                <div class="date-label">Fecha Emisión</div>
                <div class="date-value">{{ $invoice->fecha_emision->format('d/m/Y') }}</div>
            </div>
            <div class="date-item">
                <div class="date-label">Período / Ciclo</div>
                <div class="date-value">{{ $invoice->ciclo }}</div>
            </div>
            <div class="date-item">
                <div class="date-label">Fecha Vencimiento</div>
                <div class="date-value">{{ $invoice->fecha_vencimiento->format('d/m/Y') }}</div>
            </div>
        </div>
        
        <!-- CONTENIDO PRINCIPAL -->
        <div class="main-content">
            <!-- DATOS DEL SUSCRIPTOR -->
            <div class="section-box" style="display: flex; flex-direction: column; height: 100%;">
                <div class="section-header blue">👤 DATOS DEL SUSCRIPTOR</div>
                <div class="section-body" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div class="info-grid">
                        <span class="info-label">Matrícula:</span>
                        <span class="info-value highlight">{{ $invoice->subscriber->matricula }}</span>
                        
                        <span class="info-label">Nombre:</span>
                        <span class="info-value">{{ $invoice->subscriber->full_name }}</span>
                        
                        <span class="info-label">Documento:</span>
                        <span class="info-value">{{ $invoice->subscriber->cedula_nit }}</span>
                        
                        <span class="info-label">Dirección:</span>
                        <span class="info-value">{{ $invoice->subscriber->direccion }}</span>
                        
                        <span class="info-label">Sector:</span>
                        <span class="info-value">{{ $invoice->subscriber->sector ?? 'N/A' }}</span>
                        
                        <span class="info-label">Estrato:</span>
                        <span class="info-value">{{ $invoice->subscriber->estrato ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            
            <!-- DETALLE DE CONSUMO -->
            <div class="section-box" style="display: flex; flex-direction: column; height: 100%;">
                <div class="section-header green">📊 DETALLE DE CONSUMO Y TARIFA</div>
                <div class="section-body" style="padding: 0; flex: 1; display: flex; flex-direction: column;">
                    @if($invoice->reading)
                    <table class="consumption-table">
                        <thead>
                            <tr>
                                <th>Lectura Anterior</th>
                                <th>Lectura Actual</th>
                                <th>Consumo m³</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ number_format($invoice->reading->lectura_anterior, 0) }}</td>
                                <td>{{ number_format($invoice->reading->lectura_actual, 0) }}</td>
                                <td class="big-number">{{ number_format($invoice->reading->consumo, 0) }} m³</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- DESGLOSE DE TARIFA INTEGRADO -->
                    @php
                        $anioFactura = $invoice->fecha_emision->year;
                        $priceSetting = \App\Models\PriceSetting::getActiveForYear($anioFactura);
                        
                        $consumoActual = $invoice->reading->consumo;
                        $tarifaBasica = 0;
                        $metrosAdicionales = 0;
                        $recargo = 0;
                        $totalTarifa = 0;
                        
                        if ($priceSetting) {
                            $tarifaBasica = $priceSetting->cuota_basica;
                            
                            if ($consumoActual > $priceSetting->consumo_basico) {
                                $metrosAdicionales = $consumoActual - $priceSetting->consumo_basico;
                                $recargo = $metrosAdicionales * $priceSetting->tarifa_adicional;
                            }
                            
                            $totalTarifa = $tarifaBasica + $recargo;
                        }
                    @endphp
                    
                    <div style="padding: 8px; background: linear-gradient(to bottom, #f8f9fa, #fff); border-top: 1px solid #e0e0e0; flex: 1;">
                        <div style="display: grid; grid-template-columns: 1fr auto; gap: 4px; max-width: 100%;">
                            <!-- Tarifa Básica -->
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <span style="font-size: 12px;">📋</span>
                                <div>
                                    <div style="font-size: 8px; color: #666;">
                                        Tarifa Básica 
                                        @if($priceSetting)
                                            (hasta {{ number_format($priceSetting->consumo_basico, 0) }} m³)
                                        @endif
                                    </div>
                                    <div style="font-size: 9px; font-weight: 600; color: var(--color-azul);">Cuota Fija</div>
                                </div>
                            </div>
                            <div style="font-size: 12px; font-weight: bold; color: var(--color-azul); text-align: right; align-self: center;">
                                ${{ number_format($tarifaBasica, 0, ',', '.') }}
                            </div>
                            
                            @if($metrosAdicionales > 0)
                            <!-- Recargo -->
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <span style="font-size: 12px;">📈</span>
                                <div>
                                    <div style="font-size: 8px; color: #666;">Recargo metros adicionales</div>
                                    <div style="font-size: 9px; font-weight: 600; color: var(--color-verde);">
                                        {{ number_format($metrosAdicionales, 1) }} m³ × ${{ number_format($priceSetting->tarifa_adicional, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div style="font-size: 12px; font-weight: bold; color: var(--color-verde); text-align: right; align-self: center;">
                                +${{ number_format($recargo, 0, ',', '.') }}
                            </div>
                            @endif
                            
                            <!-- Total -->
                            <div style="grid-column: span 2; border-top: 1px solid var(--color-azul); margin: 5px 0 3px 0; padding-top: 5px;">
                                <div style="display: grid; grid-template-columns: 1fr auto; gap: 4px;">
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <span style="font-size: 14px;">💵</span>
                                        <div style="font-size: 9px; color: #333; font-weight: bold; text-transform: uppercase;">Total Consumo</div>
                                    </div>
                                    <div style="font-size: 14px; font-weight: bold; color: var(--color-azul-oscuro); text-align: right;">
                                        ${{ number_format($totalTarifa, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <p style="padding: 15px; text-align: center; color: #666;">Sin lectura asociada</p>
                    @endif
                </div>
            </div>
            
            <!-- GRÁFICO DE CONSUMO - SIEMPRE 3 BARRAS -->
            <div class="section-box chart-section">
                <div class="section-header mixed">📈 HISTORIAL DE CONSUMO (Últimos 3 períodos)</div>
                <div class="section-body">
                    @php
                        // Preparar datos para 3 períodos
                        $consumoActual = $invoice->reading ? $invoice->reading->consumo : 0;
                        $historial = $historialConsumo->take(3)->reverse()->values();
                        
                        // Construir array de 3 períodos
                        $periodos = [];
                        $fechaRef = $invoice->reading ? $invoice->reading->fecha : now();
                        $periodoActual = \Carbon\Carbon::parse($fechaRef);
                        
                        // Período 3 (hace 2 meses)
                        if ($historial->count() >= 3) {
                            $periodos[] = [
                                'consumo' => $historial[0]->consumo,
                                'label' => \Carbon\Carbon::parse($historial[0]->fecha)->format('M Y')
                            ];
                        } else {
                            $periodos[] = ['consumo' => 0, 'label' => $periodoActual->copy()->subMonths(2)->format('M Y')];
                        }
                        
                        // Período 2 (hace 1 mes)
                        if ($historial->count() >= 2) {
                            $idx = $historial->count() >= 3 ? 1 : 0;
                            $periodos[] = [
                                'consumo' => $historial[$idx]->consumo,
                                'label' => \Carbon\Carbon::parse($historial[$idx]->fecha)->format('M Y')
                            ];
                        } elseif ($historial->count() == 1) {
                            $periodos[] = ['consumo' => 0, 'label' => $periodoActual->copy()->subMonth()->format('M Y')];
                        } else {
                            $periodos[] = ['consumo' => 0, 'label' => $periodoActual->copy()->subMonth()->format('M Y')];
                        }
                        
                        // Período actual
                        $periodos[] = [
                            'consumo' => $consumoActual,
                            'label' => $periodoActual->format('M Y'),
                            'current' => true
                        ];
                        
                        // Calcular máximo para escalar (el actual = 100%)
                        $maxConsumo = max($consumoActual, 1);
                        foreach ($periodos as $p) {
                            if ($p['consumo'] > $maxConsumo) $maxConsumo = $p['consumo'];
                        }
                        
                        // Estadísticas basadas en historial real
                        $todosConsumos = $historialConsumo->pluck('consumo');
                        $promedio = $todosConsumos->count() > 0 ? $todosConsumos->avg() : $consumoActual;
                        $minimo = $todosConsumos->count() > 0 ? $todosConsumos->min() : $consumoActual;
                        $maximo = $todosConsumos->count() > 0 ? $todosConsumos->max() : $consumoActual;
                    @endphp
                    
                    <div class="chart-wrapper">
                        <div class="chart-bars-container">
                            @foreach($periodos as $index => $periodo)
                            <div class="bar-column">
                                <span class="bar-value">{{ number_format($periodo['consumo'], 0) }} m³</span>
                                <div class="bar-wrapper">
                                    @php
                                        $altura = $periodo['consumo'] > 0 
                                            ? max(5, ($periodo['consumo'] / $maxConsumo) * 100) 
                                            : 5;
                                    @endphp
                                    <div class="bar-fill {{ isset($periodo['current']) ? 'current' : '' }} {{ $periodo['consumo'] == 0 ? 'empty' : '' }}" 
                                         style="height: {{ $altura }}%;"></div>
                                </div>
                                <span class="bar-label">{{ $periodo['label'] }}</span>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="chart-stats-box">
                            <div class="stat-card">
                                <div class="stat-label">Promedio</div>
                                <div class="stat-value">{{ number_format($promedio, 1) }} m³</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-label">Mín / Máx</div>
                                <div class="stat-value">{{ number_format($minimo, 0) }} / {{ number_format($maximo, 0) }}</div>
                            </div>
                            <div class="stat-card green">
                                <div class="stat-label">Actual</div>
                                <div class="stat-value">{{ number_format($consumoActual, 0) }} m³</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- TOTALES -->
            <div class="totals-section">
                <div class="totals-grid">
                    <div class="total-box">
                        <div class="total-label">Subtotal</div>
                        <div class="total-value">${{ number_format($invoice->subtotal, 0, ',', '.') }}</div>
                    </div>
                    <div class="total-box highlight">
                        <div class="total-label">Total a Pagar</div>
                        <div class="total-value">${{ number_format($invoice->total, 0, ',', '.') }}</div>
                    </div>
                    <div class="total-box {{ $invoice->saldo > 0 ? 'danger' : 'success' }}">
                        <div class="total-label">Saldo Pendiente</div>
                        <div class="total-value">${{ number_format($invoice->saldo, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            
            <!-- CRÉDITOS/DEUDAS Y FACTURAS PENDIENTES -->
            @php
                // Obtener facturas anteriores pendientes del mismo suscriptor
                $facturasPendientes = \App\Models\Invoice::where('subscriber_id', $invoice->subscriber_id)
                    ->where('id', '!=', $invoice->id)
                    ->whereIn('estado', ['pendiente', 'parcial'])
                    ->where('saldo', '>', 0)
                    ->orderBy('fecha_emision', 'asc')
                    ->get();
                
                $totalFacturasPendientes = $facturasPendientes->sum('saldo');
                $totalDeudasPendientes = $totalCreditosPendientes + $totalFacturasPendientes;
            @endphp
            
            @if($totalDeudasPendientes > 0)
            <div class="section-box" style="grid-column: span 2;">
                <div class="section-header red">⚠️ DEUDAS PENDIENTES DEL SUSCRIPTOR</div>
                <div class="section-body" style="padding: 6px;">
                    
                    @if($facturasPendientes->count() > 0)
                    <div style="margin-bottom: 8px;">
                        <h4 style="font-size: 9px; color: #dc3545; margin-bottom: 4px; font-weight: bold;">📋 FACTURAS ANTERIORES PENDIENTES</h4>
                        <table class="credits-table">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Período</th>
                                    <th>Fecha</th>
                                    <th style="text-align: right;">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facturasPendientes as $factura)
                                <tr>
                                    <td>{{ $factura->numero }}</td>
                                    <td>{{ $factura->ciclo }}</td>
                                    <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                                    <td style="text-align: right; font-weight: bold; color: #dc3545;">${{ number_format($factura->saldo, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="text-align: right; font-size: 9px; font-weight: bold; color: #dc3545; margin-top: 3px; padding-top: 3px; border-top: 1px solid #ffcdd2;">
                            Subtotal: ${{ number_format($totalFacturasPendientes, 0, ',', '.') }}
                        </div>
                    </div>
                    @endif
                    
                    @if($totalCreditosPendientes > 0)
                    <div>
                        <h4 style="font-size: 9px; color: #dc3545; margin-bottom: 4px; font-weight: bold;">💳 CRÉDITOS Y OTRAS DEUDAS</h4>
                        <table class="credits-table">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Tipo</th>
                                    <th>Concepto</th>
                                    <th style="text-align: right;">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($creditosPendientes as $credito)
                                <tr>
                                    <td>{{ $credito->numero }}</td>
                                    <td>
                                        @if($credito->tipo === 'credito') Créd.
                                        @elseif($credito->tipo === 'deuda') Deuda
                                        @else C.Pend.
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($credito->concepto, 35) }}</td>
                                    <td style="text-align: right; font-weight: bold;">${{ number_format($credito->saldo, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="text-align: right; font-size: 9px; font-weight: bold; color: #dc3545; margin-top: 3px; padding-top: 3px; border-top: 1px solid #ffcdd2;">
                            Subtotal: ${{ number_format($totalCreditosPendientes, 0, ',', '.') }}
                        </div>
                    </div>
                    @endif
                    
                    <div style="text-align: center; font-size: 11px; font-weight: bold; color: #dc3545; margin-top: 6px; padding: 6px; background: #ffebee; border-radius: 4px;">
                        💰 TOTAL DEUDAS: ${{ number_format($totalDeudasPendientes, 0, ',', '.') }}
                    </div>
                    
                    <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 5px; margin-top: 5px; font-size: 7px; color: #856404; line-height: 1.3;">
                        <strong>⚠️ IMPORTANTE:</strong> Este monto NO está incluido en el total de esta factura. Regularice su situación realizando el pago de estas deudas.
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- PIE DE PÁGINA -->
        <div class="footer">
            @if($company?->mensaje_factura)
                <div class="footer-message">"{{ $company->mensaje_factura }}"</div>
            @endif
            
            @if($company?->banco && $company?->cuenta_bancaria)
            <div class="payment-box">
                💰 <strong>Pague en:</strong> {{ $company->banco }} - Cuenta No. {{ $company->cuenta_bancaria }}
            </div>
            @endif
            
            <div class="footer-info">
                {{ $company->nombre ?? 'ACUEDUCTO RURAL' }} | NIT: {{ $company->nit ?? '' }} | 
                {{ $company->direccion ?? '' }} | Tel: {{ $company->telefono ?? '' }}<br>
                Documento generado: {{ now()->format('d/m/Y H:i') }} | Conserve este recibo como comprobante de pago
            </div>
        </div>
    </div>
</body>
</html>
