<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago {{ $payment->numero_recibo }}</title>
    <style>
        @page {
            size: letter;
            margin: 0.5cm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
        }
        .page {
            width: 21.59cm;
            height: 27.94cm;
            display: flex;
            flex-direction: column;
        }
        .receipt {
            height: 50%;
            padding: 15px 20px;
            border-bottom: 2px dashed #999;
            position: relative;
        }
        .receipt:last-child {
            border-bottom: none;
        }
        .receipt-label {
            position: absolute;
            top: 5px;
            right: 15px;
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #198754;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo {
            max-height: 50px;
        }
        .company-info {
            line-height: 1.2;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #198754;
        }
        .company-details {
            font-size: 10px;
            color: #666;
        }
        .header-right {
            text-align: right;
        }
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            color: #198754;
        }
        .receipt-number {
            font-size: 14px;
            font-weight: bold;
            margin-top: 3px;
        }
        .paid-stamp {
            display: inline-block;
            padding: 3px 15px;
            background: #198754;
            color: white;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
        }
        .section-title {
            font-weight: bold;
            color: #198754;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 8px;
            font-size: 12px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .info-label {
            color: #666;
        }
        .info-value {
            font-weight: bold;
            text-align: right;
        }
        
        .amount-box {
            background: #d4edda;
            border: 2px solid #198754;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-bottom: 15px;
        }
        .amount-label {
            font-size: 12px;
            color: #155724;
            margin-bottom: 5px;
        }
        .amount-value {
            font-size: 28px;
            font-weight: bold;
            color: #155724;
        }
        
        .invoice-status {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }
        .invoice-status-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
        .invoice-status-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            text-align: center;
        }
        .status-item {
            background: white;
            padding: 5px;
            border-radius: 3px;
        }
        .status-label {
            font-size: 9px;
            color: #666;
        }
        .status-value {
            font-weight: bold;
            font-size: 13px;
        }
        .status-pending {
            color: #dc3545;
        }
        .status-paid {
            color: #198754;
        }
        
        .pending-debts {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .pending-debts-title {
            font-weight: bold;
            color: #721c24;
            margin-bottom: 5px;
            font-size: 11px;
        }
        .pending-debts-list {
            font-size: 10px;
            color: #721c24;
        }
        
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .signature-box {
            width: 150px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 25px;
            padding-top: 3px;
            font-size: 9px;
        }
        .footer-info {
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .footer-message {
            font-style: italic;
            color: #198754;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #198754;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        .print-btn:hover {
            background: #157347;
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
        🖨️ Imprimir Recibo
    </button>
    
    <div class="page">
        <!-- COPIA CLIENTE -->
        <div class="receipt">
            <div class="receipt-label">📋 COPIA CLIENTE</div>
            
            <div class="header">
                <div class="header-left">
                    @if($company?->logo)
                        <img src="{{ $company->logo_url }}" alt="Logo" class="logo">
                    @else
                        <span style="font-size: 35px;">💧</span>
                    @endif
                    <div class="company-info">
                        <div class="company-name">{{ $company->nombre ?? 'ACUEDUCTO RURAL' }}</div>
                        <div class="company-details">
                            NIT: {{ $company->nit ?? '' }} | Tel: {{ $company->telefono ?? '' }}<br>
                            {{ $company->direccion ?? '' }} - {{ $company->municipio ?? '' }}
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="receipt-title">RECIBO DE PAGO</div>
                    <div class="receipt-number">No. {{ $payment->numero_recibo }}</div>
                    <div class="paid-stamp">✓ PAGADO</div>
                </div>
            </div>
            
            <div class="content">
                <div class="section">
                    <div class="section-title">👤 DATOS DEL SUSCRIPTOR</div>
                    <div class="info-row">
                        <span class="info-label">Matrícula:</span>
                        <span class="info-value">{{ $payment->subscriber->matricula }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value">{{ $payment->subscriber->full_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Documento:</span>
                        <span class="info-value">{{ $payment->subscriber->documento }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dirección:</span>
                        <span class="info-value">{{ $payment->subscriber->direccion }}</span>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">📄 DATOS DEL PAGO</div>
                    <div class="info-row">
                        <span class="info-label">Cuota Familiar:</span>
                        <span class="info-value">{{ $payment->invoice->numero }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ciclo:</span>
                        <span class="info-value">{{ $payment->invoice->ciclo }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha de Pago:</span>
                        <span class="info-value">{{ $payment->fecha->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Método:</span>
                        <span class="info-value">{{ \App\Models\Payment::$metodosPago[$payment->metodo_pago] ?? ucfirst($payment->metodo_pago) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="amount-box">
                <div class="amount-label">VALOR PAGADO</div>
                <div class="amount-value">${{ number_format($payment->monto, 0, ',', '.') }}</div>
            </div>
            
            <div class="invoice-status">
                <div class="invoice-status-title">📊 ESTADO DE LA CUOTA {{ $payment->invoice->numero }}</div>
                <div class="invoice-status-grid">
                    <div class="status-item">
                        <div class="status-label">TOTAL CUOTA</div>
                        <div class="status-value">${{ number_format($payment->invoice->total, 0, ',', '.') }}</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">TOTAL ABONADO</div>
                        <div class="status-value status-paid">${{ number_format($payment->invoice->total - $payment->invoice->saldo, 0, ',', '.') }}</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">SALDO PENDIENTE</div>
                        <div class="status-value {{ $payment->invoice->saldo > 0 ? 'status-pending' : 'status-paid' }}">
                            ${{ number_format($payment->invoice->saldo, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
            
            @if($otrasDeudasPendientes->count() > 0)
            <div class="pending-debts">
                <div class="pending-debts-title">⚠️ OTRAS CUOTAS PENDIENTES DE PAGO:</div>
                <div class="pending-debts-list">
                    @foreach($otrasDeudasPendientes as $deuda)
                        {{ $deuda->numero }} ({{ $deuda->ciclo }}) - Saldo: ${{ number_format($deuda->saldo, 0, ',', '.') }}@if(!$loop->last), @endif
                    @endforeach
                </div>
            </div>
            @endif
            
            <div class="footer">
                <div class="signature-box">
                    <div class="signature-line">Firma Recibidor</div>
                </div>
                <div class="footer-info">
                    @if($company?->mensaje_factura)
                        <div class="footer-message">"{{ $company->mensaje_factura }}"</div>
                    @endif
                    Documento generado: {{ now()->format('d/m/Y H:i') }}<br>
                    @if($company?->banco && $company?->cuenta_bancaria)
                        Cuenta: {{ $company->banco }} - {{ $company->cuenta_bancaria }}
                    @endif
                </div>
                <div class="signature-box">
                    <div class="signature-line">Firma Cliente</div>
                </div>
            </div>
        </div>
        
        <!-- COPIA EMPRESA -->
        <div class="receipt">
            <div class="receipt-label">📋 COPIA EMPRESA</div>
            
            <div class="header">
                <div class="header-left">
                    @if($company?->logo)
                        <img src="{{ $company->logo_url }}" alt="Logo" class="logo">
                    @else
                        <span style="font-size: 35px;">💧</span>
                    @endif
                    <div class="company-info">
                        <div class="company-name">{{ $company->nombre ?? 'ACUEDUCTO RURAL' }}</div>
                        <div class="company-details">
                            NIT: {{ $company->nit ?? '' }} | Tel: {{ $company->telefono ?? '' }}<br>
                            {{ $company->direccion ?? '' }} - {{ $company->municipio ?? '' }}
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="receipt-title">RECIBO DE PAGO</div>
                    <div class="receipt-number">No. {{ $payment->numero_recibo }}</div>
                    <div class="paid-stamp">✓ PAGADO</div>
                </div>
            </div>
            
            <div class="content">
                <div class="section">
                    <div class="section-title">👤 DATOS DEL SUSCRIPTOR</div>
                    <div class="info-row">
                        <span class="info-label">Matrícula:</span>
                        <span class="info-value">{{ $payment->subscriber->matricula }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value">{{ $payment->subscriber->full_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Documento:</span>
                        <span class="info-value">{{ $payment->subscriber->documento }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dirección:</span>
                        <span class="info-value">{{ $payment->subscriber->direccion }}</span>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">📄 DATOS DEL PAGO</div>
                    <div class="info-row">
                        <span class="info-label">Cuota Familiar:</span>
                        <span class="info-value">{{ $payment->invoice->numero }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ciclo:</span>
                        <span class="info-value">{{ $payment->invoice->ciclo }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha de Pago:</span>
                        <span class="info-value">{{ $payment->fecha->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Método:</span>
                        <span class="info-value">{{ \App\Models\Payment::$metodosPago[$payment->metodo_pago] ?? ucfirst($payment->metodo_pago) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="amount-box">
                <div class="amount-label">VALOR PAGADO</div>
                <div class="amount-value">${{ number_format($payment->monto, 0, ',', '.') }}</div>
            </div>
            
            <div class="invoice-status">
                <div class="invoice-status-title">📊 ESTADO DE LA CUOTA {{ $payment->invoice->numero }}</div>
                <div class="invoice-status-grid">
                    <div class="status-item">
                        <div class="status-label">TOTAL CUOTA</div>
                        <div class="status-value">${{ number_format($payment->invoice->total, 0, ',', '.') }}</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">TOTAL ABONADO</div>
                        <div class="status-value status-paid">${{ number_format($payment->invoice->total - $payment->invoice->saldo, 0, ',', '.') }}</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">SALDO PENDIENTE</div>
                        <div class="status-value {{ $payment->invoice->saldo > 0 ? 'status-pending' : 'status-paid' }}">
                            ${{ number_format($payment->invoice->saldo, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
            
            @if($otrasDeudasPendientes->count() > 0)
            <div class="pending-debts">
                <div class="pending-debts-title">⚠️ OTRAS CUOTAS PENDIENTES DE PAGO:</div>
                <div class="pending-debts-list">
                    @foreach($otrasDeudasPendientes as $deuda)
                        {{ $deuda->numero }} ({{ $deuda->ciclo }}) - Saldo: ${{ number_format($deuda->saldo, 0, ',', '.') }}@if(!$loop->last), @endif
                    @endforeach
                </div>
            </div>
            @endif
            
            <div class="footer">
                <div class="signature-box">
                    <div class="signature-line">Firma Recibidor</div>
                </div>
                <div class="footer-info">
                    @if($company?->mensaje_factura)
                        <div class="footer-message">"{{ $company->mensaje_factura }}"</div>
                    @endif
                    Documento generado: {{ now()->format('d/m/Y H:i') }}<br>
                    @if($company?->banco && $company?->cuenta_bancaria)
                        Cuenta: {{ $company->banco }} - {{ $company->cuenta_bancaria }}
                    @endif
                </div>
                <div class="signature-box">
                    <div class="signature-line">Firma Cliente</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
