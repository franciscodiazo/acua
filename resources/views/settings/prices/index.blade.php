@extends('layouts.app')

@section('title', 'Configuración de Tarifas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-currency-dollar me-2"></i>Configuración de Tarifas</h2>
    <a href="{{ route('settings.prices.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Configuración
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Año</th>
                        <th>Consumo Básico (m³)</th>
                        <th>Cuota Básica</th>
                        <th>Tarifa Adicional (por m³)</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($settings as $setting)
                    <tr>
                        <td><strong>{{ $setting->anio }}</strong></td>
                        <td>{{ number_format($setting->consumo_basico, 0) }} m³</td>
                        <td>${{ number_format($setting->cuota_basica, 0, ',', '.') }}</td>
                        <td>${{ number_format($setting->tarifa_adicional, 0, ',', '.') }}</td>
                        <td>
                            @if($setting->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('settings.prices.edit', $setting) }}" class="btn btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('settings.prices.destroy', $setting) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar esta configuración?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No hay configuraciones de tarifas. <a href="{{ route('settings.prices.create') }}">Crear una</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($settings->hasPages())
    <div class="card-footer">
        {{ $settings->links() }}
    </div>
    @endif
</div>

<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-info-circle me-1"></i> Información de Tarifas
    </div>
    <div class="card-body">
        <p><strong>Consumo Básico:</strong> Cantidad de metros cúbicos incluidos en la cuota básica.</p>
        <p><strong>Cuota Básica:</strong> Valor a cobrar cuando el consumo es menor o igual al consumo básico.</p>
        <p><strong>Tarifa Adicional:</strong> Valor por cada metro cúbico que exceda el consumo básico.</p>
        <hr>
        <p class="mb-0"><em>Ejemplo: Si el consumo básico es 40m³, cuota básica $25,000 y tarifa adicional $1,500:</em></p>
        <ul class="mb-0">
            <li>Consumo de 35m³ = $25,000 (cuota básica)</li>
            <li>Consumo de 50m³ = $25,000 + (10 × $1,500) = $40,000</li>
        </ul>
    </div>
</div>
@endsection
