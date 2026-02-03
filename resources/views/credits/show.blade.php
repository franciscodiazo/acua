@extends('layouts.app')

@section('title', 'Detalle Crédito')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-credit-card me-2"></i>Detalle de Crédito</h2>
    <div>
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
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-1"></i> Información del Crédito
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">ID:</th>
                        <td><strong>{{ $credit->id }}</strong></td>
                    </tr>
                    <tr>
                        <th>Concepto:</th>
                        <td>{{ $credit->concepto }}</td>
                    </tr>
                    <tr>
                        <th>Monto:</th>
                        <td><strong class="fs-4 text-primary">${{ number_format($credit->monto, 0, ',', '.') }}</strong></td>
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
        <div class="card">
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
                        <th>Documento:</th>
                        <td>{{ $credit->subscriber->documento }}</td>
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
@endsection
