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
                <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <!-- Logo -->
                        <div class="col-md-12">
                            <label class="form-label">Logo de la Empresa</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                @if($company?->logo)
                                    <div class="position-relative">
                                        <img src="{{ $company->logo_url }}" alt="Logo" 
                                             class="img-thumbnail" style="max-height: 100px;">
                                        <a href="{{ route('settings.company.delete-logo') }}" 
                                           class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                           onclick="return confirm('¿Eliminar el logo?')">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    </div>
                                @else
                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                         style="width: 100px; height: 100px;">
                                        <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" 
                                           accept="image/*">
                                    <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB. Este logo aparecerá en las facturas.</small>
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

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
                    
                    <!-- Información Bancaria -->
                    <h5 class="mb-3"><i class="bi bi-bank me-2"></i>Información Bancaria</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Banco</label>
                            <input type="text" name="banco" class="form-control @error('banco') is-invalid @enderror" 
                                   value="{{ old('banco', $company?->banco) }}" placeholder="Ej: Banco Agrario">
                            @error('banco')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Cuenta Bancaria</label>
                            <input type="text" name="cuenta_bancaria" class="form-control @error('cuenta_bancaria') is-invalid @enderror" 
                                   value="{{ old('cuenta_bancaria', $company?->cuenta_bancaria) }}" placeholder="Número de cuenta">
                            @error('cuenta_bancaria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Mensaje de Factura -->
                    <h5 class="mb-3"><i class="bi bi-chat-quote me-2"></i>Mensaje en Cuotas Familiares</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Mensaje Personalizado</label>
                            <textarea name="mensaje_factura" class="form-control @error('mensaje_factura') is-invalid @enderror" 
                                      rows="2" placeholder="Ej: Cuando Proteges el Agua, Proteges la Vida">{{ old('mensaje_factura', $company?->mensaje_factura) }}</textarea>
                            <small class="text-muted">Este mensaje aparecerá en la parte inferior de las cuotas familiares.</small>
                            @error('mensaje_factura')
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
        <div class="card bg-light mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle me-1"></i> Información
            </div>
            <div class="card-body">
                <p>Esta información aparecerá en las cuotas familiares y recibos generados por el sistema.</p>
                <p class="mb-0">Asegúrese de mantener la información actualizada para que los documentos reflejen correctamente los datos de su acueducto.</p>
            </div>
        </div>
        
        <div class="card bg-info bg-opacity-10 border-info">
            <div class="card-header bg-info bg-opacity-25">
                <i class="bi bi-lightbulb me-1"></i> Tip
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Logo:</strong> Se recomienda una imagen cuadrada de al menos 200x200 píxeles.</p>
                <p class="mb-2"><strong>Cuenta Bancaria:</strong> Será visible en las cuotas para facilitar el pago por transferencia.</p>
                <p class="mb-0"><strong>Mensaje:</strong> Use un mensaje motivacional sobre el cuidado del agua.</p>
            </div>
        </div>
    </div>
</div>
@endsection
