<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuota Familiar {{ $invoice->numero }}</title>
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
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            max-width: 21.59cm;
            margin: 0 auto;
            padding: 20px;
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
            flex: 1;
        }
        .header-right {
            text-align: right;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 11px;
            color: #666;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #0d6efd;
        }
        .invoice-number {
            font-size: 14px;
            margin-top: 5px;
            font-weight: bold;
        }
        .invoice-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .status-pendiente { background: #ffc107; color: #000; }
        .status-pagada { background: #198754; color: #fff; }
        .status-parcial { background: #fd7e14; color: #fff; }
        .status-anulada { background: #dc3545; color: #fff; }
        
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0d6efd;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            text-align: right;
        }
        
        .consumption-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .consumption-table th,
        .consumption-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: center;
        }
        .consumption-table th {
            background: #0d6efd;
            color: #fff;
        }
        .consumption-table .highlight {
            background: #e7f1ff;
            font-weight: bold;
        }
        
        .totals-box {
            background: #f8f9fa;
            border: 2px solid #0d6efd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .totals-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            text-align: center;
        }
        .total-item {
            border-right: 1px solid #dee2e6;
        }
        .total-item:last-child {
            border-right: none;
        }
        .total-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .total-value {
            font-size: 20px;
            font-weight: bold;
        }
        .total-pending {
            color: #dc3545;
        }
        .total-paid {
            color: #198754;
        }
        
        .dates-bar {
            display: flex;
            justify-content: space-between;
            background: #e9ecef;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .payment-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .payment-info-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0,0,0,0.05);
            font-weight: bold;
            z-index: -1;
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
        🖨️ Imprimir Cuota Familiar
    </button>
    
    @if($invoice->estado === 'anulada')
    <div class="watermark">ANULADA</div>
    @elseif($invoice->estado === 'pagada')
    <div class="watermark">PAGADA</div>
    @endif
    
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <div class="header-left">
                @if($company?->logo)
                    <img src="{{ $company->logo_url }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">
                @else
                    <span style="color: #0d6efd; font-size: 40px;">💧</span>
                @endif
                <div class="company-name">
                    {{ $company->nombre ?? 'ACUEDUCTO RURAL' }}
                </div>
                <div class="company-info">
                    @if($company)
                        NIT: {{ $company->nit }}<br>
                        {{ $company->direccion }}<br>
                        {{ $company->municipio }}, {{ $company->departamento }}<br>
                        Tel: {{ $company->telefono }} | {{ $company->email }}
                    @else
                        Configure la información de la empresa
                    @endif
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">CUOTA FAMILIAR</div>
                <div class="invoice-number">{{ $invoice->numero }}</div>
                <div class="invoice-status status-{{ $invoice->estado }}">
                    {{ strtoupper($invoice->estado) }}
                </div>
            </div>
        </div>
        
        <!-- Barra de fechas -->
        <div class="dates-bar">
            <div><strong>Fecha de Emisión:</strong> {{ $invoice->fecha_emision->format('d/m/Y') }}</div>
            <div><strong>Ciclo:</strong> {{ $invoice->ciclo }}</div>
            <div><strong>Fecha de Vencimiento:</strong> {{ $invoice->fecha_vencimiento->format('d/m/Y') }}</div>
        </div>
        
        <!-- Información del suscriptor -->
        <div class="section">
            <div class="section-title">📋 INFORMACIÓN DEL SUSCRIPTOR</div>
            <div class="info-grid">
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Matrícula:</span>
                        <span class="info-value"><strong>{{ $invoice->subscriber->matricula }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Documento:</span>
                        <span class="info-value">{{ $invoice->subscriber->documento }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value">{{ $invoice->subscriber->full_name }}</span>
                    </div>
                </div>
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Dirección:</span>
                        <span class="info-value">{{ $invoice->subscriber->direccion }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Sector:</span>
                        <span class="info-value">{{ $invoice->subscriber->sector ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Estrato:</span>
                        <span class="info-value">{{ $invoice->subscriber->estrato }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detalle de consumo -->
        @if($invoice->reading)
        <div class="section">
            <div class="section-title">📊 DETALLE DE CONSUMO</div>
            <table class="consumption-table">
                <thead>
                    <tr>
                        <th>Lectura Anterior</th>
                        <th>Lectura Actual</th>
                        <th>Consumo (m³)</th>
                        <th>Valor Unitario</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ number_format($invoice->reading->lectura_anterior, 0) }}</td>
                        <td>{{ number_format($invoice->reading->lectura_actual, 0) }}</td>
                        <td class="highlight">{{ number_format($invoice->reading->consumo, 0) }} m³</td>
                        <td>-</td>
                        <td class="highlight">${{ number_format($invoice->reading->valor_total, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Totales -->
        <div class="totals-box">
            <div class="totals-grid">
                <div class="total-item">
                    <div class="total-label">SUBTOTAL</div>
                    <div class="total-value">${{ number_format($invoice->subtotal, 0, ',', '.') }}</div>
                </div>
                <div class="total-item">
                    <div class="total-label">TOTAL FACTURA</div>
                    <div class="total-value">${{ number_format($invoice->total, 0, ',', '.') }}</div>
                </div>
                <div class="total-item">
                    <div class="total-label">SALDO PENDIENTE</div>
                    <div class="total-value {{ $invoice->saldo > 0 ? 'total-pending' : 'total-paid' }}">
                        ${{ number_format($invoice->saldo, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Información de pago -->
        <div class="payment-info">
            <div class="payment-info-title">💰 INFORMACIÓN DE PAGO</div>
            <p>Pague oportunamente antes de la fecha de vencimiento para evitar recargos e intereses.</p>
            @if($company?->banco && $company?->cuenta_bancaria)
                <p><strong>Cuenta para consignación:</strong> {{ $company->banco }} - No. {{ $company->cuenta_bancaria }}</p>
            @endif
            <p>Puntos de pago: Oficina del Acueducto o transferencia a cuenta autorizada.</p>
            <p><strong>Conserve este recibo como comprobante de su cuota familiar.</strong></p>
        </div>
        
        <!-- Historial de pagos -->
        @if($invoice->payments->count() > 0)
        <div class="section" style="margin-top: 20px;">
            <div class="section-title">💳 HISTORIAL DE PAGOS</div>
            <table class="consumption-table">
                <thead>
                    <tr>
                        <th>No. Recibo</th>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{ $payment->numero_recibo }}</td>
                        <td>{{ $payment->fecha->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($payment->metodo_pago) }}</td>
                        <td>${{ number_format($payment->monto, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($payment->estado) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Pie de página -->
        <div class="footer">
            @if($company?->mensaje_factura)
                <p style="font-size: 14px; font-weight: bold; color: #0d6efd; margin-bottom: 15px;">
                    "{{ $company->mensaje_factura }}"
                </p>
            @endif
            <p>{{ $company->nombre ?? 'ACUEDUCTO RURAL' }} - {{ $company->nit ?? '' }}</p>
            <p>{{ $company->direccion ?? '' }} | Tel: {{ $company->telefono ?? '' }}</p>
            <p>Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
            <p><em>Este documento es válido como cuota familiar de servicios de acueducto</em></p>
        </div>
    </div>
</body>
</html>
