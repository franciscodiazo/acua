@extends('layouts.app')

@section('title', 'Información de la Empresa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-building me-2"></i>Información de la Empresa</h2>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('settings.company.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre de la Empresa <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                   value="{{ old('nombre', $company?->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">NIT <span class="text-danger">*</span></label>
                            <input type="text" name="nit" class="form-control @error('nit') is-invalid @enderror" 
                                   value="{{ old('nit', $company?->nit) }}" required>
                            @error('nit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" 
                                   value="{{ old('direccion', $company?->direccion) }}">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Municipio</label>
                            <input type="text" name="municipio" class="form-control @error('municipio') is-invalid @enderror" 
                                   value="{{ old('municipio', $company?->municipio) }}">
                            @error('municipio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Departamento</label>
                            <input type="text" name="departamento" class="form-control @error('departamento') is-invalid @enderror" 
                                   value="{{ old('departamento', $company?->departamento) }}">
                            @error('departamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                                   value="{{ old('telefono', $company?->telefono) }}">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $company?->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Representante Legal</label>
                            <input type="text" name="representante_legal" class="form-control @error('representante_legal') is-invalid @enderror" 
                                   value="{{ old('representante_legal', $company?->representante_legal) }}">
                            @error('representante_legal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Guardar Información
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-header">
                <i class="bi bi-info-circle me-1"></i> Información
            </div>
            <div class="card-body">
                <p>Esta información aparecerá en las facturas y recibos generados por el sistema.</p>
                <p class="mb-0">Asegúrese de mantener la información actualizada para que los documentos reflejen correctamente los datos de su acueducto.</p>
            </div>
        </div>
    </div>
</div>
@endsection
