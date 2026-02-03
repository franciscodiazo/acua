@extends('layouts.app')

@section('title', 'Nuevo Suscriptor')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-plus me-2"></i>Nuevo Suscriptor</h2>
    <a href="{{ route('subscribers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('subscribers.store') }}" method="POST">
            @csrf
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Matrícula <span class="text-danger">*</span></label>
                    <input type="text" name="matricula" class="form-control @error('matricula') is-invalid @enderror" 
                           value="{{ old('matricula') }}" required>
                    @error('matricula')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Documento <span class="text-danger">*</span></label>
                    <input type="text" name="documento" class="form-control @error('documento') is-invalid @enderror" 
                           value="{{ old('documento') }}" required>
                    @error('documento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Estrato <span class="text-danger">*</span></label>
                    <select name="estrato" class="form-select @error('estrato') is-invalid @enderror" required>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('estrato', 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    @error('estrato')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                    <input type="text" name="apellidos" class="form-control @error('apellidos') is-invalid @enderror" 
                           value="{{ old('apellidos') }}" required>
                    @error('apellidos')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror" 
                           value="{{ old('nombres') }}" required>
                    @error('nombres')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" 
                           value="{{ old('correo') }}">
                    @error('correo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                           value="{{ old('telefono') }}">
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">No. Personas <span class="text-danger">*</span></label>
                    <input type="number" name="no_personas" class="form-control @error('no_personas') is-invalid @enderror" 
                           value="{{ old('no_personas', 1) }}" min="1" required>
                    @error('no_personas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Sector</label>
                    <input type="text" name="sector" class="form-control @error('sector') is-invalid @enderror" 
                           value="{{ old('sector') }}">
                    @error('sector')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-8">
                    <label class="form-label">Dirección <span class="text-danger">*</span></label>
                    <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" 
                           value="{{ old('direccion') }}" required>
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Guardar Suscriptor
                </button>
                <a href="{{ route('subscribers.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
