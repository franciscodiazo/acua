@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted me-3">{{ now()->format('d/m/Y') }}</span>
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-file-earmark-text me-1"></i> Reportes
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header">Movimientos</h6></li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalDiario">
                        <i class="bi bi-calendar-day me-2"></i>Movimiento Diario
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalFechas">
                        <i class="bi bi-calendar-range me-2"></i>Por Rango de Fechas
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalAnual">
                        <i class="bi bi-calendar-check me-2"></i>Cierre Anual
                    </a>
                </li>
            </ul>
        </div>
    </div>
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

<!-- Modal Reporte Diario -->
<div class="modal fade" id="modalDiario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('reportes.diario') }}" method="GET" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-day me-2"></i>Reporte Movimiento Diario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Seleccione la fecha</label>
                        <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reporte por Fechas -->
<div class="modal fade" id="modalFechas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('reportes.fechas') }}" method="GET" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-range me-2"></i>Reporte por Rango de Fechas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" 
                                   value="{{ date('Y-m-01') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cierre Anual -->
<div class="modal fade" id="modalAnual" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('reportes.cierre-anual') }}" method="GET" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-check me-2"></i>Cierre Anual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el año</label>
                        <select name="anio" class="form-select" required>
                            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Generar Cierre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
