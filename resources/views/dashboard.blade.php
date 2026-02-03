@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
    <span class="text-muted">{{ now()->format('d/m/Y') }}</span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-people fs-3 text-primary"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Suscriptores Activos</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_subscribers']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded">
                        <i class="bi bi-speedometer fs-3 text-warning"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Lecturas Pendientes</h6>
                        <h3 class="mb-0">{{ number_format($stats['readings_pendientes']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded">
                        <i class="bi bi-receipt fs-3 text-danger"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Por Cobrar</h6>
                        <h3 class="mb-0">${{ number_format($stats['saldo_por_cobrar'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-cash-coin fs-3 text-success"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Recaudado (Mes)</h6>
                        <h3 class="mb-0">${{ number_format($stats['total_recaudado_mes'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-speedometer me-2"></i>Últimas Lecturas</span>
                <a href="{{ route('readings.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Nueva
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Suscriptor</th>
                                <th>Ciclo</th>
                                <th>Consumo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasLecturas as $lectura)
                            <tr>
                                <td>{{ $lectura->subscriber->matricula }}</td>
                                <td>{{ $lectura->subscriber->full_name }}</td>
                                <td>{{ $lectura->ciclo }}</td>
                                <td>{{ number_format($lectura->consumo, 0) }} m³</td>
                                <td>
                                    <span class="badge badge-{{ $lectura->estado }}">{{ ucfirst($lectura->estado) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay lecturas registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash-coin me-2"></i>Últimos Pagos</span>
                <a href="{{ route('payments.create') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus"></i> Nuevo
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Recibo</th>
                                <th>Suscriptor</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosAbonos as $pago)
                            <tr>
                                <td>{{ $pago->numero_recibo }}</td>
                                <td>{{ $pago->subscriber->full_name }}</td>
                                <td>${{ number_format($pago->monto, 0, ',', '.') }}</td>
                                <td>{{ $pago->fecha->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No hay pagos registrados</td>
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
