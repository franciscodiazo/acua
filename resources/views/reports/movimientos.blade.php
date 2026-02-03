<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        @page {
            size: letter;
            margin: 1cm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            max-width: 21.59cm;
            margin: 0 auto;
            padding: 15px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo {
            max-height: 50px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
        }
        .company-info {
            font-size: 10px;
            color: #666;
        }
        .header-right {
            text-align: right;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
        }
        .report-subtitle {
            font-size: 12px;
            color: #666;
        }
        .report-date {
            font-size: 10px;
            color: #999;
            margin-top: 5px;
        }
        
        .summary-boxes {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .summary-box {
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .summary-box.primary {
            background: #cfe2ff;
            border: 1px solid #b6d4fe;
        }
        .summary-box.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .summary-box.warning {
            background: #fff3cd;
            border: 1px solid #ffeeba;
        }
        .summary-box.danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: #666;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }
        .primary .summary-value { color: #084298; }
        .success .summary-value { color: #155724; }
        .warning .summary-value { color: #856404; }
        .danger .summary-value { color: #721c24; }
        
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background: #0d6efd;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #198754;
        }
        .text-danger {
            color: #dc3545;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-activo { background: #198754; color: #fff; }
        .badge-anulado { background: #dc3545; color: #fff; }
        
        .totals-row {
            background: #e9ecef !important;
            font-weight: bold;
        }
        
        .method-summary {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .method-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
        }
        .method-label {
            font-size: 9px;
            color: #666;
        }
        .method-value {
            font-size: 14px;
            font-weight: bold;
            color: #198754;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #0d6efd;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #0b5ed7;
        }
        
        @media print {
            .print-btn {
                display: none;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">
        🖨️ Imprimir Reporte
    </button>
    
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <div class="header-left">
                @if($company?->logo)
                    <img src="{{ $company->logo_url }}" alt="Logo" class="logo">
                @else
                    <span style="font-size: 40px; color: #0d6efd;">💧</span>
                @endif
                <div>
                    <div class="company-name">{{ $company->nombre ?? 'ACUEDUCTO RURAL' }}</div>
                    <div class="company-info">
                        NIT: {{ $company->nit ?? '' }}<br>
                        {{ $company->direccion ?? '' }}
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="report-title">{{ $titulo }}</div>
                <div class="report-subtitle">{{ $subtitulo }}</div>
                <div class="report-date">Generado: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        
        <!-- Resumen -->
        <div class="summary-boxes">
            <div class="summary-box primary">
                <div class="summary-label">Total Recibos</div>
                <div class="summary-value">{{ $resumen['total_recibos'] }}</div>
            </div>
            <div class="summary-box success">
                <div class="summary-label">Total Recaudado</div>
                <div class="summary-value">${{ number_format($resumen['total_recaudado'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-box warning">
                <div class="summary-label">Cuotas Facturadas</div>
                <div class="summary-value">{{ $resumen['cuotas_facturadas'] }}</div>
            </div>
            <div class="summary-box danger">
                <div class="summary-label">Anulaciones</div>
                <div class="summary-value">{{ $resumen['anulaciones'] }}</div>
            </div>
        </div>
        
        <!-- Resumen por Método de Pago -->
        @if(isset($resumenMetodos))
        <div class="section">
            <div class="section-title">💳 RECAUDO POR MÉTODO DE PAGO</div>
            <div class="method-summary">
                @foreach(\App\Models\Payment::$metodosPago as $key => $label)
                <div class="method-box">
                    <div class="method-label">{{ $label }}</div>
                    <div class="method-value">${{ number_format($resumenMetodos[$key] ?? 0, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Detalle de Pagos -->
        @if($pagos->count() > 0)
        <div class="section">
            <div class="section-title">💰 DETALLE DE PAGOS</div>
            <table>
                <thead>
                    <tr>
                        <th>No. Recibo</th>
                        <th>Matrícula</th>
                        <th>Suscriptor</th>
                        <th>Cuota</th>
                        <th class="text-center">Fecha</th>
                        <th>Método</th>
                        <th class="text-right">Monto</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagos as $pago)
                    <tr>
                        <td><strong>{{ $pago->numero_recibo }}</strong></td>
                        <td>{{ $pago->subscriber->matricula }}</td>
                        <td>{{ $pago->subscriber->full_name }}</td>
                        <td>{{ $pago->invoice->numero }}</td>
                        <td class="text-center">{{ $pago->fecha->format('d/m/Y') }}</td>
                        <td>{{ \App\Models\Payment::$metodosPago[$pago->metodo_pago] ?? ucfirst($pago->metodo_pago) }}</td>
                        <td class="text-right text-success"><strong>${{ number_format($pago->monto, 0, ',', '.') }}</strong></td>
                        <td class="text-center">
                            <span class="badge badge-{{ $pago->estado }}">{{ ucfirst($pago->estado) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="totals-row">
                        <td colspan="6"><strong>TOTAL</strong></td>
                        <td class="text-right text-success"><strong>${{ number_format($pagos->where('estado', 'activo')->sum('monto'), 0, ',', '.') }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="section">
            <div class="section-title">💰 DETALLE DE PAGOS</div>
            <p style="text-align: center; padding: 20px; color: #666;">No se encontraron pagos en el período seleccionado.</p>
        </div>
        @endif
        
        <!-- Cuotas Emitidas -->
        @if(isset($cuotasEmitidas) && $cuotasEmitidas->count() > 0)
        <div class="section">
            <div class="section-title">📋 CUOTAS FAMILIARES EMITIDAS</div>
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Matrícula</th>
                        <th>Suscriptor</th>
                        <th>Ciclo</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Saldo</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cuotasEmitidas as $cuota)
                    <tr>
                        <td><strong>{{ $cuota->numero }}</strong></td>
                        <td>{{ $cuota->subscriber->matricula }}</td>
                        <td>{{ $cuota->subscriber->full_name }}</td>
                        <td>{{ $cuota->ciclo }}</td>
                        <td class="text-right">${{ number_format($cuota->total, 0, ',', '.') }}</td>
                        <td class="text-right {{ $cuota->saldo > 0 ? 'text-danger' : 'text-success' }}">
                            ${{ number_format($cuota->saldo, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-{{ $cuota->estado }}">{{ ucfirst($cuota->estado) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="totals-row">
                        <td colspan="4"><strong>TOTALES</strong></td>
                        <td class="text-right"><strong>${{ number_format($cuotasEmitidas->sum('total'), 0, ',', '.') }}</strong></td>
                        <td class="text-right text-danger"><strong>${{ number_format($cuotasEmitidas->sum('saldo'), 0, ',', '.') }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
        
        <!-- Pie de página -->
        <div class="footer">
            <p>{{ $company->nombre ?? 'ACUEDUCTO RURAL' }} - NIT: {{ $company->nit ?? '' }}</p>
            <p>{{ $company->direccion ?? '' }} | Tel: {{ $company->telefono ?? '' }}</p>
        </div>
    </div>
</body>
</html>
