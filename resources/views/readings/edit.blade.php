@extends('layouts.app')

@section('title', 'Editar Lectura')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer me-2"></i>Editar Lectura</h2>
    <a href="{{ route('readings.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Suscriptor:</strong> {{ $reading->subscriber->matricula }} - {{ $reading->subscriber->full_name }}<br>
                    <strong>Ciclo:</strong> {{ $reading->ciclo }}
                </div>
                
                <form action="{{ route('readings.update', $reading) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" 
                                   value="{{ old('fecha', $reading->fecha->format('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Lectura Anterior</label>
                            <input type="text" class="form-control" 
                                   value="{{ number_format($reading->lectura_anterior, 0) }}" readonly>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Lectura Actual <span class="text-danger">*</span></label>
                            <input type="number" name="lectura_actual" id="lectura_actual" 
                                   class="form-control @error('lectura_actual') is-invalid @enderror" 
                                   value="{{ old('lectura_actual', $reading->lectura_actual) }}" 
                                   min="{{ $reading->lectura_anterior }}" step="1" required>
                            @error('lectura_actual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Actualizar Lectura
                        </button>
                        <a href="{{ route('readings.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
