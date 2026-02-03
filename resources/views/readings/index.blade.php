@extends('layouts.app')

@section('title', 'Lecturas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer me-2"></i>Lecturas</h2>
    <a href="{{ route('readings.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Lectura
    </a>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('readings.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar suscriptor..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="ciclo" class="form-select">
                    <option value="">Todos los ciclos</option>
                    @foreach($ciclos as $ciclo)
                        <option value="{{ $ciclo }}" {{ request('ciclo') == $ciclo ? 'selected' : '' }}>
                            {{ $ciclo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="facturado" {{ request('estado') == 'facturado' ? 'selected' : '' }}>Facturado</option>
                    <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                @if(request('search') || request('ciclo') || request('estado'))
                    <a href="{{ route('readings.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Barra de acciones de facturación -->
    <div class="card-header bg-light border-top">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">
                        <strong>Seleccionar todas</strong>
                    </label>
                </div>
            </div>
            <div class="col-auto">
                <span id="selectedCount" class="badge bg-secondary">0 seleccionadas</span>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-success btn-sm" id="btnFacturarSeleccionadas" disabled
                        onclick="facturarSeleccionadas()">
                    <i class="bi bi-receipt me-1"></i> Facturar Seleccionadas
                </button>
            </div>
            <div class="col-auto ms-auto">
                <form action="{{ route('invoices.facturar') }}" method="POST" class="d-inline" 
                      onsubmit="return confirm('¿Está seguro de facturar TODAS las lecturas pendientes del ciclo seleccionado?')">
                    @csrf
                    <div class="input-group input-group-sm">
                        <select name="ciclo" class="form-select form-select-sm" required style="min-width: 120px;">
                            <option value="">Ciclo...</option>
                            @foreach($ciclos as $ciclo)
                                <option value="{{ $ciclo }}">{{ $ciclo }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="bi bi-receipt-cutoff me-1"></i> Facturar Todo el Ciclo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <form id="formFacturarSeleccionadas" action="{{ route('invoices.facturar.seleccionadas') }}" method="POST">
        @csrf
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="40">
                                <i class="bi bi-check2-square"></i>
                            </th>
                            <th>ID</th>
                            <th>Matrícula</th>
                            <th>Suscriptor</th>
                            <th>Ciclo</th>
                            <th>Fecha</th>
                            <th>Lect. Anterior</th>
                            <th>Lect. Actual</th>
                            <th>Consumo</th>
                            <th>Valor Total</th>
                            <th>Estado</th>
                            <th width="130">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($readings as $reading)
                        <tr>
                            <td>
                                @if($reading->estado === 'pendiente')
                                <input type="checkbox" class="form-check-input reading-checkbox" 
                                       name="readings[]" value="{{ $reading->id }}">
                                @endif
                            </td>
                            <td>{{ $reading->id }}</td>
                            <td><strong>{{ $reading->subscriber->matricula }}</strong></td>
                            <td>{{ $reading->subscriber->full_name }}</td>
                            <td>{{ $reading->ciclo }}</td>
                            <td>{{ $reading->fecha->format('d/m/Y') }}</td>
                            <td>{{ number_format($reading->lectura_anterior, 0) }}</td>
                            <td>{{ number_format($reading->lectura_actual, 0) }}</td>
                            <td>{{ number_format($reading->consumo, 0) }} m³</td>
                            <td>${{ number_format($reading->valor_total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $reading->estado }}">{{ ucfirst($reading->estado) }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('readings.show', $reading) }}" class="btn btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($reading->estado === 'pendiente')
                                    <a href="{{ route('readings.edit', $reading) }}" class="btn btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('invoices.facturar.individual', $reading) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Facturar esta lectura?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Facturar">
                                            <i class="bi bi-receipt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                No se encontraron lecturas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
            </table>
        </div>
    </div>
    </form>
    @if($readings->hasPages())
    <div class="card-footer">
        {{ $readings->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Seleccionar todas
    $('#selectAll').change(function() {
        $('.reading-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectedCount();
    });
    
    // Actualizar contador al cambiar checkbox individual
    $('.reading-checkbox').change(function() {
        updateSelectedCount();
        
        // Actualizar estado del selectAll
        const total = $('.reading-checkbox').length;
        const checked = $('.reading-checkbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
        $('#selectAll').prop('indeterminate', checked > 0 && checked < total);
    });
    
    function updateSelectedCount() {
        const count = $('.reading-checkbox:checked').length;
        $('#selectedCount').text(count + ' seleccionadas');
        $('#btnFacturarSeleccionadas').prop('disabled', count === 0);
        
        if (count > 0) {
            $('#selectedCount').removeClass('bg-secondary').addClass('bg-primary');
        } else {
            $('#selectedCount').removeClass('bg-primary').addClass('bg-secondary');
        }
    }
});

function facturarSeleccionadas() {
    const count = $('.reading-checkbox:checked').length;
    if (count === 0) {
        alert('Debe seleccionar al menos una lectura');
        return;
    }
    
    if (confirm('¿Está seguro de facturar las ' + count + ' lecturas seleccionadas?')) {
        $('#formFacturarSeleccionadas').submit();
    }
}
</script>
@endpush