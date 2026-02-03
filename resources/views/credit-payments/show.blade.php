@extends('layouts.app')

@section('title', 'Detalle de Abono')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt me-2"></i>Detalle de Abono #{{ $creditPayment->numero_recibo }}</h2>
    <div>
        <a href="{{ route('credit-payments.print', $creditPayment) }}" class="btn btn-primary" target="_blank">
            <i class="bi bi-printer me-1"></i> Imprimir Recibo
        </a>
        <a href="{{ route('credit-payments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-credit-card me-1"></i> Información del Abono
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th class="text-muted" style="width: 40%">Recibo #:</th>
                        <td>{{ $creditPayment->numero_recibo }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Fecha:</th>
                        <td>{{ \Carbon\Carbon::parse($creditPayment->fecha)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Monto Abonado:</th>
                        <td class="text-success fw-bold fs-5">${{ number_format($creditPayment->monto, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Método de Pago:</th>
                        <td>
                            <span class="badge bg-secondary">
                                {{ \App\Models\CreditPayment::$metodosPago[$creditPayment->metodo_pago] ?? $creditPayment->metodo_pago }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Estado:</th>
                        <td>
                            @if($creditPayment->anulado)
                                <span class="badge bg-danger">Anulado</span>
                            @else
                                <span class="badge bg-success">Activo</span>
                            @endif
                        </td>
                    </tr>
                    @if($creditPayment->observaciones)
                    <tr>
                        <th class="text-muted">Observaciones:</th>
                        <td>{{ $creditPayment->observaciones }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-person me-1"></i> Información del Suscriptor
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th class="text-muted" style="width: 40%">Matrícula:</th>
                        <td>{{ $creditPayment->subscriber->matricula }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Nombre:</th>
                        <td>{{ $creditPayment->subscriber->full_name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Cédula/NIT:</th>
                        <td>{{ $creditPayment->subscriber->cedula_nit }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Dirección:</th>
                        <td>{{ $creditPayment->subscriber->direccion }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cash-coin me-1"></i> Información del Crédito
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th class="text-muted" style="width: 40%">Crédito #:</th>
                        <td>
                            <a href="{{ route('credits.show', $creditPayment->credit) }}">
                                {{ $creditPayment->credit->numero }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tipo:</th>
                        <td>
                            @php
                                $tiposCredito = [
                                    'credito' => ['label' => 'Crédito', 'class' => 'bg-primary'],
                                    'deuda' => ['label' => 'Deuda', 'class' => 'bg-danger'],
                                    'cuota' => ['label' => 'Cuota Pendiente', 'class' => 'bg-warning']
                                ];
                                $tipo = $tiposCredito[$creditPayment->credit->tipo] ?? ['label' => 'Otro', 'class' => 'bg-secondary'];
                            @endphp
                            <span class="badge {{ $tipo['class'] }}">{{ $tipo['label'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Concepto:</th>
                        <td>{{ $creditPayment->credit->concepto }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Monto Original:</th>
                        <td>${{ number_format($creditPayment->credit->monto, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Saldo Actual:</th>
                        <td class="{{ $creditPayment->credit->saldo > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                            ${{ number_format($creditPayment->credit->saldo, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if(!$creditPayment->anulado)
<div class="mt-4">
    <form action="{{ route('credit-payments.anular', $creditPayment) }}" method="POST" class="d-inline" 
          onsubmit="return confirm('¿Está seguro de anular este abono? Se restaurará el saldo del crédito.')">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-outline-danger">
            <i class="bi bi-x-circle me-1"></i> Anular Abono
        </button>
    </form>
</div>
@endif
@endsection
