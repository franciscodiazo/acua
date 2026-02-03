@extends('layouts.app')

@section('title', 'Detalle Lectura')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer me-2"></i>Detalle de Lectura</h2>
    <div>
        @if($reading->estado === 'pendiente')
        <a href="{{ route('readings.edit', $reading) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Editar
        </a>
        @endif
        <a href="{{ route('readings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-1"></i> Información de la Lectura
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Ciclo:</th>
                        <td><strong>{{ $reading->ciclo }}</strong></td>
                    </tr>
                    <tr>
                        <th>Fecha:</th>
                        <td>{{ $reading->fecha->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Lectura Anterior:</th>
                        <td>{{ number_format($reading->lectura_anterior, 0) }}</td>
                    </tr>
                    <tr>
                        <th>Lectura Actual:</th>
                        <td>{{ number_format($reading->lectura_actual, 0) }}</td>
                    </tr>
                    <tr>
                        <th>Consumo:</th>
                        <td><strong class="text-primary">{{ number_format($reading->consumo, 0) }} m³</strong></td>
                    </tr>
                    <tr>
                        <th>Valor Total:</th>
                        <td><strong class="text-success fs-4">${{ number_format($reading->valor_total, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            <span class="badge badge-{{ $reading->estado }} fs-6">{{ ucfirst($reading->estado) }}</span>
                        </td>
                    </tr>
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
                        <td><strong>{{ $reading->subscriber->matricula }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ $reading->subscriber->full_name }}</td>
                    </tr>
                    <tr>
                        <th>Documento:</th>
                        <td>{{ $reading->subscriber->documento }}</td>
                    </tr>
                    <tr>
                        <th>Dirección:</th>
                        <td>{{ $reading->subscriber->direccion }}</td>
                    </tr>
                    <tr>
                        <th>Sector:</th>
                        <td>{{ $reading->subscriber->sector ?? '-' }}</td>
                    </tr>
                </table>
                <a href="{{ route('subscribers.show', $reading->subscriber) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye me-1"></i> Ver Suscriptor
                </a>
            </div>
        </div>
        
        @if($reading->invoice)
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-receipt me-1"></i> Factura Generada
            </div>
            <div class="card-body">
                <p><strong>Número:</strong> {{ $reading->invoice->numero }}</p>
                <p><strong>Estado:</strong> 
                    <span class="badge badge-{{ $reading->invoice->estado }}">{{ ucfirst($reading->invoice->estado) }}</span>
                </p>
                <a href="{{ route('invoices.show', $reading->invoice) }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-eye me-1"></i> Ver Factura
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
