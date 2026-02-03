@extends('layouts.app')

@section('title', 'Detalle Suscriptor')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person me-2"></i>{{ $subscriber->full_name }}</h2>
    <div>
        <a href="{{ route('readings.create', ['subscriber_id' => $subscriber->id]) }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Nueva Lectura
        </a>
        <a href="{{ route('subscribers.edit', $subscriber) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Editar
        </a>
        <a href="{{ route('subscribers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-info-circle me-1"></i> Información del Suscriptor
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="40%">Matrícula:</th>
                        <td><strong>{{ $subscriber->matricula }}</strong></td>
                    </tr>
                    <tr>
                        <th>Documento:</th>
                        <td>{{ $subscriber->documento }}</td>
                    </tr>
                    <tr>
                        <th>Nombres:</th>
                        <td>{{ $subscriber->nombres }}</td>
                    </tr>
                    <tr>
                        <th>Apellidos:</th>
                        <td>{{ $subscriber->apellidos }}</td>
                    </tr>
                    <tr>
                        <th>Dirección:</th>
                        <td>{{ $subscriber->direccion }}</td>
                    </tr>
                    <tr>
                        <th>Sector:</th>
                        <td>{{ $subscriber->sector ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Estrato:</th>
                        <td>{{ $subscriber->estrato }}</td>
                    </tr>
                    <tr>
                        <th>Teléfono:</th>
                        <td>{{ $subscriber->telefono ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Correo:</th>
                        <td>{{ $subscriber->correo ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No. Personas:</th>
                        <td>{{ $subscriber->no_personas }}</td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            @if($subscriber->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-speedometer me-1"></i> Historial de Lecturas
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Ciclo</th>
                                <th>Fecha</th>
                                <th>Lect. Anterior</th>
                                <th>Lect. Actual</th>
                                <th>Consumo</th>
                                <th>Valor</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriber->readings as $reading)
                            <tr>
                                <td>{{ $reading->ciclo }}</td>
                                <td>{{ $reading->fecha->format('d/m/Y') }}</td>
                                <td>{{ number_format($reading->lectura_anterior, 0) }}</td>
                                <td>{{ number_format($reading->lectura_actual, 0) }}</td>
                                <td>{{ number_format($reading->consumo, 0) }} m³</td>
                                <td>${{ number_format($reading->valor_total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-{{ $reading->estado }}">{{ ucfirst($reading->estado) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">Sin lecturas registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <i class="bi bi-receipt me-1"></i> Historial de Facturas
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Ciclo</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Saldo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriber->invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->numero }}</td>
                                <td>{{ $invoice->ciclo }}</td>
                                <td>{{ $invoice->fecha_emision->format('d/m/Y') }}</td>
                                <td>${{ number_format($invoice->total, 0, ',', '.') }}</td>
                                <td>${{ number_format($invoice->saldo, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-{{ $invoice->estado }}">{{ ucfirst($invoice->estado) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">Sin facturas registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
