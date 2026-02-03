@extends('layouts.app')

@section('title', 'Detalle Crédito')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-credit-card me-2"></i>Crédito #{{ $credit->numero }}</h2>
    <div>
        @if($credit->estado === 'activo' && $credit->saldo > 0)
        <a href="{{ route('credit-payments.create', ['credit_id' => $credit->id]) }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Registrar Abono
        </a>
        @endif
        @if($credit->estado === 'activo')
        <form action="{{ route('credits.anular', $credit) }}" method="POST" class="d-inline" 
              onsubmit="return confirm('¿Está seguro de anular este crédito?')">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-x-circle me-1"></i> Anular
            </button>
        </form>
        @endif
        <a href="{{ route('credits.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-1"></i> Información del Crédito
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Número:</th>
                        <td><strong>{{ $credit->numero }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tipo:</th>
                        <td>
                            @php
                                $tiposCredito = [
                                    'credito' => ['label' => 'Crédito', 'class' => 'bg-primary'],
                                    'deuda' => ['label' => 'Deuda', 'class' => 'bg-danger'],
                                    'cuota' => ['label' => 'Cuota Pendiente', 'class' => 'bg-warning text-dark']
                                ];
                                $tipo = $tiposCredito[$credit->tipo] ?? ['label' => 'Otro', 'class' => 'bg-secondary'];
                            @endphp
                            <span class="badge {{ $tipo['class'] }} fs-6">{{ $tipo['label'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Concepto:</th>
                        <td>{{ $credit->concepto }}</td>
                    </tr>
                    <tr>
                        <th>Monto Original:</th>
                        <td><strong class="fs-5">${{ number_format($credit->monto, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Saldo Pendiente:</th>
                        <td>
                            <strong class="fs-4 {{ $credit->saldo > 0 ? 'text-danger' : 'text-success' }}">
                                ${{ number_format($credit->saldo, 0, ',', '.') }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <th>Fecha:</th>
                        <td>{{ $credit->fecha->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            <span class="badge badge-{{ $credit->estado }} fs-6">{{ ucfirst($credit->estado) }}</span>
                        </td>
                    </tr>
                    @if($credit->observaciones)
                    <tr>
                        <th>Observaciones:</th>
                        <td>{{ $credit->observaciones }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-person me-1"></i> Información del Suscriptor
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Matrícula:</th>
                        <td><strong>{{ $credit->subscriber->matricula }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ $credit->subscriber->full_name }}</td>
                    </tr>
                    <tr>
                        <th>Cédula/NIT:</th>
                        <td>{{ $credit->subscriber->cedula_nit }}</td>
                    </tr>
                    <tr>
                        <th>Dirección:</th>
                        <td>{{ $credit->subscriber->direccion }}</td>
                    </tr>
                </table>
                <a href="{{ route('subscribers.show', $credit->subscriber) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye me-1"></i> Ver Suscriptor
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Historial de Abonos -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-1"></i> Historial de Abonos</span>
        @if($credit->estado === 'activo' && $credit->saldo > 0)
        <a href="{{ route('credit-payments.create', ['credit_id' => $credit->id]) }}" class="btn btn-sm btn-success">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Abono
        </a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Recibo #</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($credit->payments as $payment)
                    <tr class="{{ $payment->anulado ? 'table-danger' : '' }}">
                        <td>{{ $payment->numero_recibo }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->fecha)->format('d/m/Y') }}</td>
                        <td>${{ number_format($payment->monto, 0, ',', '.') }}</td>
                        <td>{{ \App\Models\CreditPayment::$metodosPago[$payment->metodo_pago] ?? $payment->metodo_pago }}</td>
                        <td>
                            @if($payment->anulado)
                                <span class="badge bg-danger">Anulado</span>
                            @else
                                <span class="badge bg-success">Activo</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('credit-payments.show', $payment) }}" class="btn btn-outline-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('credit-payments.print', $payment) }}" class="btn btn-outline-primary" target="_blank" title="Imprimir">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No hay abonos registrados para este crédito
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
