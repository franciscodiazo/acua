@extends('layouts.app')

@section('title', 'Detalle Pago')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cash-coin me-2"></i>Recibo de Pago {{ $payment->numero_recibo }}</h2>
    <div>
        <a href="{{ route('payments.print', $payment) }}" class="btn btn-secondary" target="_blank">
            <i class="bi bi-printer me-1"></i> Imprimir Recibo
        </a>
        @if($payment->estado === 'activo')
        <form action="{{ route('payments.anular', $payment) }}" method="POST" class="d-inline" 
              onsubmit="return confirm('¿Está seguro de anular este pago?')">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-x-circle me-1"></i> Anular
            </button>
        </form>
        @endif
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-1"></i> Información del Pago
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">No. Recibo:</th>
                        <td><strong>{{ $payment->numero_recibo }}</strong></td>
                    </tr>
                    <tr>
                        <th>Factura:</th>
                        <td>
                            <a href="{{ route('invoices.show', $payment->invoice) }}">
                                {{ $payment->invoice->numero }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Fecha:</th>
                        <td>{{ $payment->fecha->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Monto:</th>
                        <td><strong class="fs-4 text-success">${{ number_format($payment->monto, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Método de Pago:</th>
                        <td>{{ ucfirst($payment->metodo_pago) }}</td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            <span class="badge badge-{{ $payment->estado }} fs-6">{{ ucfirst($payment->estado) }}</span>
                        </td>
                    </tr>
                    @if($payment->observaciones)
                    <tr>
                        <th>Observaciones:</th>
                        <td>{{ $payment->observaciones }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
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
                        <td><strong>{{ $payment->subscriber->matricula }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ $payment->subscriber->full_name }}</td>
                    </tr>
                    <tr>
                        <th>Documento:</th>
                        <td>{{ $payment->subscriber->documento }}</td>
                    </tr>
                    <tr>
                        <th>Dirección:</th>
                        <td>{{ $payment->subscriber->direccion }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
