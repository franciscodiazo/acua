@extends('layouts.app')

@section('title', 'Nueva Lectura')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer me-2"></i>Nueva Lectura</h2>
    <a href="{{ route('readings.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('readings.store') }}" method="POST" id="formLectura">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Suscriptor <span class="text-danger">*</span></label>
                            <select name="subscriber_id" id="subscriber_id" class="form-select @error('subscriber_id') is-invalid @enderror" required>
                                <option value="">Seleccione un suscriptor...</option>
                                @foreach($subscribers as $subscriber)
                                    <option value="{{ $subscriber->id }}" 
                                            {{ old('subscriber_id', $selectedSubscriber?->id) == $subscriber->id ? 'selected' : '' }}>
                                        {{ $subscriber->matricula }} - {{ $subscriber->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subscriber_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Ciclo <span class="text-danger">*</span></label>
                            <select name="ciclo" id="ciclo" class="form-select @error('ciclo') is-invalid @enderror" required>
                                @foreach($ciclos as $ciclo)
                                    <option value="{{ $ciclo }}" {{ old('ciclo') == $ciclo ? 'selected' : '' }}>
                                        {{ $ciclo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ciclo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" 
                                   value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Lectura Anterior</label>
                            <input type="text" id="lectura_anterior" class="form-control" 
                                   value="{{ number_format($lecturaAnterior, 0) }}" readonly>
                            <small class="text-muted">Valor del último registro</small>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Lectura Actual <span class="text-danger">*</span></label>
                            <input type="number" name="lectura_actual" id="lectura_actual" 
                                   class="form-control @error('lectura_actual') is-invalid @enderror" 
                                   value="{{ old('lectura_actual') }}" min="0" step="1" required>
                            @error('lectura_actual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Consumo (m³)</label>
                            <input type="text" id="consumo" class="form-control fw-bold" value="0" readonly>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Guardar Lectura
                        </button>
                        <a href="{{ route('readings.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-header">
                <i class="bi bi-calculator me-1"></i> Cálculo en Tiempo Real
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="small text-muted">Consumo Básico</label>
                    <p class="mb-0 fs-5" id="consumo_basico">40 m³</p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted">Cuota Básica</label>
                    <p class="mb-0 fs-5" id="cuota_basica">$25,000</p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted">Tarifa Adicional (por m³)</label>
                    <p class="mb-0 fs-5" id="tarifa_adicional">$1,500</p>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="small text-muted">Consumo Calculado</label>
                    <p class="mb-0 fs-4 text-primary" id="consumo_calculado">0 m³</p>
                </div>
                <div>
                    <label class="small text-muted">Valor Total a Pagar</label>
                    <p class="mb-0 fs-3 fw-bold text-success" id="valor_total">$0</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let lecturaAnterior = {{ $lecturaAnterior }};
    
    function calcular() {
        const subscriberId = $('#subscriber_id').val();
        const lecturaActual = parseFloat($('#lectura_actual').val()) || 0;
        const ciclo = $('#ciclo').val();
        
        if (!subscriberId) return;
        
        $.get('{{ route("readings.calcular") }}', {
            subscriber_id: subscriberId,
            lectura_actual: lecturaActual,
            ciclo: ciclo
        }, function(data) {
            lecturaAnterior = data.lectura_anterior;
            $('#lectura_anterior').val(data.lectura_anterior.toLocaleString('es-CO'));
            $('#consumo').val(data.consumo + ' m³');
            $('#consumo_calculado').text(data.consumo + ' m³');
            $('#valor_total').text('$' + data.valor_total.toLocaleString('es-CO'));
            $('#consumo_basico').text(data.consumo_basico + ' m³');
            $('#cuota_basica').text('$' + parseFloat(data.cuota_basica).toLocaleString('es-CO'));
            $('#tarifa_adicional').text('$' + parseFloat(data.tarifa_adicional).toLocaleString('es-CO'));
        });
    }
    
    $('#subscriber_id, #ciclo').change(function() {
        calcular();
    });
    
    $('#lectura_actual').on('input', function() {
        calcular();
    });
    
    // Calcular al cargar si hay suscriptor seleccionado
    if ($('#subscriber_id').val()) {
        calcular();
    }
});
</script>
@endpush
