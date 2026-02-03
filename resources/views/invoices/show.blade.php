@extends('layouts.app')

@section('title', 'Detalle Cuota Familiar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt me-2"></i>Cuota Familiar {{ $invoice->numero }}</h2>
    <div>
        <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-secondary" target="_blank">
            <i class="bi bi-printer me-1"></i> Imprimir
        </a>
        @if(in_array($invoice->estado, ['pendiente', 'parcial']))
        <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-success">
            <i class="bi bi-cash-coin me-1"></i> Registrar Pago
        </a>
        @endif
        @if($invoice->estado === 'pendiente')
        <form action="{{ route('invoices.anular', $invoice) }}" method="POST" class="d-inline" 
              onsubmit="return confirm('¿Está seguro de anular esta cuota familiar?')">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-x-circle me-1"></i> Anular
            </button>
        </form>
        @endif
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-1"></i> Información de la Cuota Familiar
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Número:</th>
                        <td><strong>{{ $invoice->numero }}</strong></td>
                    </tr>
                    <tr>
                        <th>Ciclo:</th>
                        <td>{{ $invoice->ciclo }}</td>
                    </tr>
                    <tr>
                        <th>Fecha Emisión:</th>
                        <td>{{ $invoice->fecha_emision->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Fecha Vencimiento:</th>
                        <td>{{ $invoice->fecha_vencimiento->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Subtotal:</th>
                        <td>${{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Descuentos:</th>
                        <td>${{ number_format($invoice->descuentos, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total:</th>
                        <td><strong class="fs-5">${{ number_format($invoice->total, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Saldo Pendiente:</th>
                        <td><strong class="fs-4 text-danger">${{ number_format($invoice->saldo, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            <span class="badge badge-{{ $invoice->estado }} fs-6">{{ ucfirst($invoice->estado) }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        @if($invoice->reading)
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-speedometer me-1"></i> Detalle de Consumo
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Lectura Anterior:</th>
                        <td>{{ number_format($invoice->reading->lectura_anterior, 0) }}</td>
                    </tr>
                    <tr>
                        <th>Lectura Actual:</th>
                        <td>{{ number_format($invoice->reading->lectura_actual, 0) }}</td>
                    </tr>
                    <tr>
                        <th>Consumo:</th>
                        <td><strong>{{ number_format($invoice->reading->consumo, 0) }} m³</strong></td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person me-1"></i> Información del Suscriptor
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Matrícula:</th>
                        <td><strong>{{ $invoice->subscriber->matricula }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ $invoice->subscriber->full_name }}</td>
                    </tr>
                    <tr>
                        <th>Documento:</th>
                        <td>{{ $invoice->subscriber->documento }}</td>
                    </tr>
                    <tr>
                        <th>Dirección:</th>
                        <td>{{ $invoice->subscriber->direccion }}</td>
                    </tr>
                    <tr>
                        <th>Estrato:</th>
                        <td>{{ $invoice->subscriber->estrato }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-cash-coin me-1"></i> Historial de Pagos
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Recibo</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->numero_recibo }}</td>
                            <td>{{ $payment->fecha->format('d/m/Y') }}</td>
                            <td>${{ number_format($payment->monto, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $payment->estado }}">{{ ucfirst($payment->estado) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Sin pagos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
