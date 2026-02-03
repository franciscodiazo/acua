@extends('layouts.app')

@section('title', 'Nuevo Crédito')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-credit-card me-2"></i>Nuevo Crédito</h2>
    <a href="{{ route('credits.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('credits.store') }}" method="POST">
            @csrf
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Suscriptor <span class="text-danger">*</span></label>
                    <select name="subscriber_id" class="form-select @error('subscriber_id') is-invalid @enderror" required>
                        <option value="">Seleccione un suscriptor...</option>
                        @foreach($subscribers as $sub)
                            <option value="{{ $sub->id }}" 
                                    {{ old('subscriber_id', $subscriber?->id) == $sub->id ? 'selected' : '' }}>
                                {{ $sub->matricula }} - {{ $sub->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subscriber_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Monto <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="monto" class="form-control @error('monto') is-invalid @enderror" 
                               value="{{ old('monto') }}" min="1" step="1" required>
                    </div>
                    @error('monto')
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
                
                <div class="col-md-12">
                    <label class="form-label">Concepto <span class="text-danger">*</span></label>
                    <input type="text" name="concepto" class="form-control @error('concepto') is-invalid @enderror" 
                           value="{{ old('concepto') }}" required>
                    @error('concepto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Guardar Crédito
                </button>
                <a href="{{ route('credits.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
