@extends('layouts.app')

@section('title', 'Editar Configuración de Tarifas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-currency-dollar me-2"></i>Editar Configuración - Año {{ $price->anio }}</h2>
    <a href="{{ route('settings.prices.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('settings.prices.update', $price) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Año</label>
                            <input type="text" class="form-control" value="{{ $price->anio }}" readonly>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Consumo Básico (m³) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="consumo_basico" class="form-control @error('consumo_basico') is-invalid @enderror" 
                                       value="{{ old('consumo_basico', $price->consumo_basico) }}" min="1" step="1" required>
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
                                       value="{{ old('cuota_basica', $price->cuota_basica) }}" min="1" step="1" required>
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
                                       value="{{ old('tarifa_adicional', $price->tarifa_adicional) }}" min="1" step="1" required>
                            </div>
                            @error('tarifa_adicional')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1"
                                       {{ old('activo', $price->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    Configuración Activa
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Actualizar Configuración
                        </button>
                        <a href="{{ route('settings.prices.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
