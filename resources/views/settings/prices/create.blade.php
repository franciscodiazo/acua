@extends('layouts.app')

@section('title', 'Nueva Configuración de Tarifas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-currency-dollar me-2"></i>Nueva Configuración de Tarifas</h2>
    <a href="{{ route('settings.prices.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('settings.prices.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Año <span class="text-danger">*</span></label>
                            <input type="number" name="anio" class="form-control @error('anio') is-invalid @enderror" 
                                   value="{{ old('anio', date('Y')) }}" min="2020" max="2100" required>
                            @error('anio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Consumo Básico (m³) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="consumo_basico" class="form-control @error('consumo_basico') is-invalid @enderror" 
                                       value="{{ old('consumo_basico', 40) }}" min="1" step="1" required>
                                <span class="input-group-text">m³</span>
                            </div>
                            @error('consumo_basico')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Cuota Básica <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="cuota_basica" class="form-control @error('cuota_basica') is-invalid @enderror" 
                                       value="{{ old('cuota_basica', 25000) }}" min="1" step="1" required>
                            </div>
                            @error('cuota_basica')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tarifa Adicional (por m³) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="tarifa_adicional" class="form-control @error('tarifa_adicional') is-invalid @enderror" 
                                       value="{{ old('tarifa_adicional', 1500) }}" min="1" step="1" required>
                            </div>
                            @error('tarifa_adicional')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Guardar Configuración
                        </button>
                        <a href="{{ route('settings.prices.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
