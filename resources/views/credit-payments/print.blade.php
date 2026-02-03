<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Abono #{{ $creditPayment->numero_recibo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: letter;
            margin: 0.3cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }
        
        .page {
            width: 100%;
            height: 27.2cm; /* Altura de carta menos márgenes */
        }
        
        .receipt {
            height: 13.2cm; /* Mitad de la página */
            padding: 10px;
            border: 1px dashed #ccc;
            position: relative;
            overflow: hidden;
        }
        
        .receipt-divider {
            border-top: 2px dashed #999;
            margin: 5px 0;
            position: relative;
            height: 20px;
        }
        
        .receipt-divider::before {
            content: '✂ Cortar aquí';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 10px;
            font-size: 9px;
            color: #666;
        }
        
        .copy-label {
            position: absolute;
            top: 3px;
            right: 8px;
            font-size: 8px;
            color: #666;
            font-style: italic;
        }
        
        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            margin-right: 10px;
        }
        
        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        .company-details {
            font-size: 9px;
            color: #666;
        }
        
        .receipt-number {
            text-align: right;
        }
        
        .receipt-number .number {
            font-size: 16px;
            font-weight: bold;
            color: #d9534f;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            background: #f5f5f5;
            padding: 4px;
            margin: 6px 0;
            border: 1px solid #ddd;
        }
        
        .info-section {
            display: flex;
            gap: 15px;
            margin-bottom: 6px;
        }
        
        .info-block {
            flex: 1;
        }
        
        .info-block h4 {
            font-size: 10px;
            background: #333;
            color: white;
            padding: 2px 6px;
            margin-bottom: 4px;
        }
        
        .info-row {
            display: flex;
            padding: 1px 0;
            border-bottom: 1px dotted #ddd;
        }
        
        .info-label {
            width: 35%;
            color: #666;
            font-size: 9px;
        }
        
        .info-value {
            flex: 1;
            font-weight: 500;
            font-size: 9px;
        }
        
        .amount-box {
            background: #f0f9f0;
            border: 2px solid #28a745;
            padding: 6px;
            text-align: center;
            margin: 6px 0;
        }
        
        .amount-label {
            font-size: 9px;
            color: #666;
        }
        
        .amount-value {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        
        .credit-summary {
            background: #f8f9fa;
            padding: 5px;
            margin: 6px 0;
            border: 1px solid #ddd;
        }
        
        .credit-summary table {
            width: 100%;
            font-size: 9px;
        }
        
        .credit-summary td {
            padding: 1px 4px;
        }
        
        .credit-summary .label {
            color: #666;
        }
        
        .credit-summary .value {
            text-align: right;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
        .signature-line {
            width: 40%;
            border-top: 1px solid #333;
            text-align: center;
            padding-top: 3px;
            font-size: 9px;
        }
        
        .bank-info {
            font-size: 8px;
            color: #666;
            text-align: center;
            margin-top: 5px;
        }
        
        .custom-message {
            font-size: 8px;
            text-align: center;
            color: #666;
            font-style: italic;
            margin-top: 3px;
        }
        
        .status-anulado {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 50px;
            color: rgba(255, 0, 0, 0.2);
            font-weight: bold;
            pointer-events: none;
        }
        
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 10px; background: #f5f5f5; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14px; cursor: pointer;">
            🖨️ Imprimir Recibo
        </button>
        <button onclick="window.close()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            ✖ Cerrar
        </button>
    </div>
    
    <div class="page">
        <!-- Copia para el Cliente -->
        <div class="receipt">
            <span class="copy-label">COPIA CLIENTE</span>
            
            @if($creditPayment->anulado)
                <div class="status-anulado">ANULADO</div>
            @endif
            
            <div class="header">
                <div class="logo">
                    @if($company && $company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo">
                    @else
                        <div style="width: 80px; height: 80px; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">LOGO</div>
                    @endif
                </div>
                <div class="company-info">
                    <div class="company-name">{{ $company->nombre ?? 'ACUEDUCTO RURAL' }}</div>
                    <div class="company-details">
                        NIT: {{ $company->nit ?? '' }}<br>
                        {{ $company->direccion ?? '' }}<br>
                        Tel: {{ $company->telefono ?? '' }}
                    </div>
                </div>
                <div class="receipt-number">
                    <small>RECIBO DE ABONO Nº</small>
                    <div class="number">{{ $creditPayment->numero_recibo }}</div>
                    <small>{{ \Carbon\Carbon::parse($creditPayment->fecha)->format('d/m/Y') }}</small>
                </div>
            </div>
            
            <div class="receipt-title">RECIBO DE ABONO A CRÉDITO/DEUDA</div>
            
            <div class="info-section">
                <div class="info-block">
                    <h4>DATOS DEL SUSCRIPTOR</h4>
                    <div class="info-row">
                        <span class="info-label">Matrícula:</span>
                        <span class="info-value">{{ $creditPayment->subscriber->matricula }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value">{{ $creditPayment->subscriber->full_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cédula/NIT:</span>
                        <span class="info-value">{{ $creditPayment->subscriber->cedula_nit }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dirección:</span>
                        <span class="info-value">{{ $creditPayment->subscriber->direccion }}</span>
                    </div>
                </div>
                <div class="info-block">
                    <h4>DATOS DEL CRÉDITO</h4>
                    <div class="info-row">
                        <span class="info-label">Crédito Nº:</span>
                        <span class="info-value">{{ $creditPayment->credit->numero }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Concepto:</span>
                        <span class="info-value">{{ $creditPayment->credit->concepto }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Método Pago:</span>
                        <span class="info-value">{{ \App\Models\CreditPayment::$metodosPago[$creditPayment->metodo_pago] ?? $creditPayment->metodo_pago }}</span>
                    </div>
                </div>
            </div>
            
            <div class="amount-box">
                <div class="amount-label">VALOR ABONADO</div>
                <div class="amount-value">${{ number_format($creditPayment->monto, 0, ',', '.') }}</div>
            </div>
            
            <div class="credit-summary">
                <table>
                    <tr>
                        <td class="label">Monto Original del Crédito:</td>
                        <td class="value">${{ number_format($creditPayment->credit->monto, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Saldo Pendiente:</td>
                        <td class="value" style="color: {{ $creditPayment->credit->saldo > 0 ? '#dc3545' : '#28a745' }}">
                            ${{ number_format($creditPayment->credit->saldo, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
            
            @if($creditPayment->observaciones)
                <div style="font-size: 10px; color: #666;">
                    <strong>Observaciones:</strong> {{ $creditPayment->observaciones }}
                </div>
            @endif
            
            <div class="footer">
                <div class="signatures">
                    <div class="signature-line">Recibido por</div>
                    <div class="signature-line">Cliente</div>
                </div>
                
                @if($company && $company->banco)
                    <div class="bank-info">
                        Pagos: {{ $company->banco }} - Cuenta {{ $company->cuenta_bancaria }}
                    </div>
                @endif
                
                @if($company && $company->mensaje_factura)
                    <div class="custom-message">{{ $company->mensaje_factura }}</div>
                @endif
            </div>
        </div>
        
        <div class="receipt-divider"></div>
        
        <!-- Copia para la Empresa -->
        <div class="receipt">
            <span class="copy-label">COPIA EMPRESA</span>
            
            @if($creditPayment->anulado)
                <div class="status-anulado">ANULADO</div>
            @endif
            
            <div class="header">
                <div class="logo">
                    @if($company && $company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo">
                    @else
                        <div style="width: 80px; height: 80px; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">LOGO</div>
                    @endif
                </div>
                <div class="company-info">
                    <div class="company-name">{{ $company->nombre ?? 'ACUEDUCTO RURAL' }}</div>
                    <div class="company-details">
                        NIT: {{ $company->nit ?? '' }}<br>
                        {{ $company->direccion ?? '' }}<br>
                        Tel: {{ $company->telefono ?? '' }}
                    </div>
                </div>
                <div class="receipt-number">
                    <small>RECIBO DE ABONO Nº</small>
                    <div class="number">{{ $creditPayment->numero_recibo }}</div>
                    <small>{{ \Carbon\Carbon::parse($creditPayment->fecha)->format('d/m/Y') }}</small>
                </div>
            </div>
            
            <div class="receipt-title">RECIBO DE ABONO A CRÉDITO/DEUDA</div>
            
            <div class="info-section">
                <div class="info-block">
                    <h4>DATOS DEL SUSCRIPTOR</h4>
                    <div class="info-row">
                        <span class="info-label">Matrícula:</span>
                        <span class="info-value">{{ $creditPayment->subscriber->matricula }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value">{{ $creditPayment->subscriber->full_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cédula/NIT:</span>
                        <span class="info-value">{{ $creditPayment->subscriber->cedula_nit }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dirección:</span>
                        <span class="info-value">{{ $creditPayment->subscriber->direccion }}</span>
                    </div>
                </div>
                <div class="info-block">
                    <h4>DATOS DEL CRÉDITO</h4>
                    <div class="info-row">
                        <span class="info-label">Crédito Nº:</span>
                        <span class="info-value">{{ $creditPayment->credit->numero }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Concepto:</span>
                        <span class="info-value">{{ $creditPayment->credit->concepto }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Método Pago:</span>
                        <span class="info-value">{{ \App\Models\CreditPayment::$metodosPago[$creditPayment->metodo_pago] ?? $creditPayment->metodo_pago }}</span>
                    </div>
                </div>
            </div>
            
            <div class="amount-box">
                <div class="amount-label">VALOR ABONADO</div>
                <div class="amount-value">${{ number_format($creditPayment->monto, 0, ',', '.') }}</div>
            </div>
            
            <div class="credit-summary">
                <table>
                    <tr>
                        <td class="label">Monto Original del Crédito:</td>
                        <td class="value">${{ number_format($creditPayment->credit->monto, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Saldo Pendiente:</td>
                        <td class="value" style="color: {{ $creditPayment->credit->saldo > 0 ? '#dc3545' : '#28a745' }}">
                            ${{ number_format($creditPayment->credit->saldo, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
            
            @if($creditPayment->observaciones)
                <div style="font-size: 10px; color: #666;">
                    <strong>Observaciones:</strong> {{ $creditPayment->observaciones }}
                </div>
            @endif
            
            <div class="footer">
                <div class="signatures">
                    <div class="signature-line">Recibido por</div>
                    <div class="signature-line">Cliente</div>
                </div>
                
                @if($company && $company->banco)
                    <div class="bank-info">
                        Pagos: {{ $company->banco }} - Cuenta {{ $company->cuenta_bancaria }}
                    </div>
                @endif
                
                @if($company && $company->mensaje_factura)
                    <div class="custom-message">{{ $company->mensaje_factura }}</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
