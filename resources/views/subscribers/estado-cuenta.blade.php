<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Cuenta - {{ $subscriber->matricula }}</title>
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
            font-size: 20px;
            font-weight: bold;
            color: #0d6efd;
        }
        .report-date {
            font-size: 11px;
            color: #666;
        }
        
        .subscriber-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .info-item {
            text-align: center;
        }
        .info-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        .info-value {
            font-size: 13px;
            font-weight: bold;
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
        .summary-box.danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .summary-box.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .summary-box.warning {
            background: #fff3cd;
            border: 1px solid #ffeeba;
        }
        .summary-box.info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
        }
        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }
        .danger .summary-value { color: #721c24; }
        .success .summary-value { color: #155724; }
        .warning .summary-value { color: #856404; }
        .info .summary-value { color: #0c5460; }
        
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
        .text-danger {
            color: #dc3545;
        }
        .text-success {
            color: #198754;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-pendiente { background: #ffc107; color: #000; }
        .badge-pagada { background: #198754; color: #fff; }
        .badge-parcial { background: #fd7e14; color: #fff; }
        .badge-anulada { background: #dc3545; color: #fff; }
        .badge-activo { background: #198754; color: #fff; }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .footer-message {
            font-style: italic;
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 5px;
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
        🖨️ Imprimir Estado de Cuenta
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
                        NIT: {{ $company->nit ?? '' }} | {{ $company->telefono ?? '' }}<br>
                        {{ $company->direccion ?? '' }}
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="report-title">ESTADO DE CUENTA</div>
                <div class="report-date">Generado: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        
        <!-- Información del Suscriptor -->
        <div class="subscriber-info">
            <div class="info-item">
                <div class="info-label">Matrícula</div>
                <div class="info-value">{{ $subscriber->matricula }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Nombre Completo</div>
                <div class="info-value">{{ $subscriber->full_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Documento</div>
                <div class="info-value">{{ $subscriber->documento }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Dirección</div>
                <div class="info-value">{{ $subscriber->direccion }}</div>
            </div>
        </div>
        
        <!-- Resumen -->
        <div class="summary-boxes">
            <div class="summary-box danger">
                <div class="summary-label">Saldo Pendiente</div>
                <div class="summary-value">${{ number_format($resumen['saldo_pendiente'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-box success">
                <div class="summary-label">Total Abonado</div>
                <div class="summary-value">${{ number_format($resumen['total_abonado'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-box warning">
                <div class="summary-label">Créditos a Favor</div>
                <div class="summary-value">${{ number_format($resumen['creditos_favor'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-box info">
                <div class="summary-label">Cuotas Pendientes</div>
                <div class="summary-value">{{ $resumen['cuotas_pendientes'] }}</div>
            </div>
        </div>
        
        <!-- Cuotas Familiares Pendientes -->
        @if($facturasPendientes->count() > 0)
        <div class="section">
            <div class="section-title">📋 CUOTAS FAMILIARES PENDIENTES</div>
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Ciclo</th>
                        <th class="text-center">Emisión</th>
                        <th class="text-center">Vencimiento</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Abonado</th>
                        <th class="text-right">Saldo</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facturasPendientes as $factura)
                    <tr>
                        <td><strong>{{ $factura->numero }}</strong></td>
                        <td>{{ $factura->ciclo }}</td>
                        <td class="text-center">{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $factura->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td class="text-right">${{ number_format($factura->total, 0, ',', '.') }}</td>
                        <td class="text-right text-success">${{ number_format($factura->total - $factura->saldo, 0, ',', '.') }}</td>
                        <td class="text-right text-danger"><strong>${{ number_format($factura->saldo, 0, ',', '.') }}</strong></td>
                        <td class="text-center">
                            <span class="badge badge-{{ $factura->estado }}">{{ ucfirst($factura->estado) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #e9ecef;">
                        <td colspan="4"><strong>TOTAL PENDIENTE</strong></td>
                        <td class="text-right"><strong>${{ number_format($facturasPendientes->sum('total'), 0, ',', '.') }}</strong></td>
                        <td class="text-right text-success"><strong>${{ number_format($facturasPendientes->sum('total') - $facturasPendientes->sum('saldo'), 0, ',', '.') }}</strong></td>
                        <td class="text-right text-danger"><strong>${{ number_format($facturasPendientes->sum('saldo'), 0, ',', '.') }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
        
        <!-- Historial de Abonos -->
        @if($abonos->count() > 0)
        <div class="section">
            <div class="section-title">💰 HISTORIAL DE ABONOS/PAGOS</div>
            <table>
                <thead>
                    <tr>
                        <th>No. Recibo</th>
                        <th>Cuota</th>
                        <th class="text-center">Fecha</th>
                        <th>Método</th>
                        <th class="text-right">Monto</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($abonos as $abono)
                    <tr>
                        <td><strong>{{ $abono->numero_recibo }}</strong></td>
                        <td>{{ $abono->invoice->numero }}</td>
                        <td class="text-center">{{ $abono->fecha->format('d/m/Y') }}</td>
                        <td>{{ \App\Models\Payment::$metodosPago[$abono->metodo_pago] ?? ucfirst($abono->metodo_pago) }}</td>
                        <td class="text-right text-success"><strong>${{ number_format($abono->monto, 0, ',', '.') }}</strong></td>
                        <td class="text-center">
                            <span class="badge badge-{{ $abono->estado }}">{{ ucfirst($abono->estado) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #e9ecef;">
                        <td colspan="4"><strong>TOTAL ABONADO</strong></td>
                        <td class="text-right text-success"><strong>${{ number_format($abonos->where('estado', 'activo')->sum('monto'), 0, ',', '.') }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
        
        <!-- Créditos a Favor -->
        @if($creditos->count() > 0)
        <div class="section">
            <div class="section-title">🎁 CRÉDITOS A FAVOR</div>
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th class="text-center">Fecha</th>
                        <th>Concepto</th>
                        <th class="text-right">Monto</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($creditos as $credito)
                    <tr>
                        <td><strong>{{ $credito->numero }}</strong></td>
                        <td class="text-center">{{ $credito->fecha->format('d/m/Y') }}</td>
                        <td>{{ $credito->concepto }}</td>
                        <td class="text-right text-success"><strong>${{ number_format($credito->monto, 0, ',', '.') }}</strong></td>
                        <td class="text-center">
                            <span class="badge badge-{{ $credito->estado }}">{{ ucfirst($credito->estado) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Historial de Cuotas (Últimas 12) -->
        @if($historialFacturas->count() > 0)
        <div class="section">
            <div class="section-title">📜 HISTORIAL DE CUOTAS FAMILIARES (Últimas 12)</div>
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Ciclo</th>
                        <th class="text-right">Consumo</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Saldo</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historialFacturas as $factura)
                    <tr>
                        <td><strong>{{ $factura->numero }}</strong></td>
                        <td>{{ $factura->ciclo }}</td>
                        <td class="text-right">{{ $factura->reading ? number_format($factura->reading->consumo, 0) . ' m³' : '-' }}</td>
                        <td class="text-right">${{ number_format($factura->total, 0, ',', '.') }}</td>
                        <td class="text-right {{ $factura->saldo > 0 ? 'text-danger' : 'text-success' }}">
                            ${{ number_format($factura->saldo, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-{{ $factura->estado }}">{{ ucfirst($factura->estado) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Pie de página -->
        <div class="footer">
            @if($company?->mensaje_factura)
                <div class="footer-message">"{{ $company->mensaje_factura }}"</div>
            @endif
            <p>{{ $company->nombre ?? 'ACUEDUCTO RURAL' }} - NIT: {{ $company->nit ?? '' }}</p>
            <p>{{ $company->direccion ?? '' }} | Tel: {{ $company->telefono ?? '' }}</p>
            @if($company?->banco && $company?->cuenta_bancaria)
                <p>Cuenta para consignación: {{ $company->banco }} - {{ $company->cuenta_bancaria }}</p>
            @endif
        </div>
    </div>
</body>
</html>
