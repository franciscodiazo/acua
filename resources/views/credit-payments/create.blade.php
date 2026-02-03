@extends('layouts.app')

@section('title', 'Nuevo Abono a Crédito')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-credit-card me-2"></i>Registrar Abono a Crédito/Deuda</h2>
    <a href="{{ route('credit-payments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('credit-payments.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Crédito/Deuda <span class="text-danger">*</span></label>
                            <select name="credit_id" id="credit_id" class="form-select @error('credit_id') is-invalid @enderror" required>
                                <option value="">Seleccione un crédito o deuda...</option>
                                @foreach($credits as $cred)
                                    <option value="{{ $cred->id }}" 
                                            data-saldo="{{ $cred->saldo }}"
                                            data-monto="{{ $cred->monto }}"
                                            data-concepto="{{ $cred->concepto }}"
                                            {{ old('credit_id', $credit?->id) == $cred->id ? 'selected' : '' }}>
                                        {{ $cred->numero }} - {{ $cred->subscriber->matricula }} - {{ $cred->subscriber->full_name }} 
                                        (Saldo: ${{ number_format($cred->saldo, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('credit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Monto a Abonar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="monto" id="monto" 
                                       class="form-control @error('monto') is-invalid @enderror" 
                                       value="{{ old('monto', $credit?->saldo) }}" min="1" step="1" required>
                            </div>
                            @error('monto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" 
                                   value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                            <select name="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror" required>
                                @foreach(\App\Models\CreditPayment::$metodosPago as $key => $label)
                                    <option value="{{ $key }}" {{ old('metodo_pago', 'efectivo') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('metodo_pago')
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
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Registrar Abono
                        </button>
                        <a href="{{ route('credit-payments.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card bg-light" id="infoCredito" style="{{ $credit ? '' : 'display:none' }}">
            <div class="card-header">
                <i class="bi bi-info-circle me-1"></i> Información del Crédito
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="small text-muted">Total Crédito</label>
                    <h4 id="totalCredito">${{ number_format($credit?->monto ?? 0, 0, ',', '.') }}</h4>
                </div>
                <div class="mb-3">
                    <label class="small text-muted">Saldo Pendiente</label>
                    <h4 class="text-danger" id="saldoCredito">${{ number_format($credit?->saldo ?? 0, 0, ',', '.') }}</h4>
                </div>
                <div class="mb-0">
                    <label class="small text-muted">Concepto</label>
                    <p id="conceptoCredito" class="mb-0">{{ $credit?->concepto ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('credit_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const infoCard = document.getElementById('infoCredito');
    
    if (this.value) {
        const saldo = selected.dataset.saldo;
        const monto = selected.dataset.monto;
        const concepto = selected.dataset.concepto;
        
        document.getElementById('totalCredito').textContent = '$' + parseInt(monto).toLocaleString('es-CO');
        document.getElementById('saldoCredito').textContent = '$' + parseInt(saldo).toLocaleString('es-CO');
        document.getElementById('conceptoCredito').textContent = concepto;
        document.getElementById('monto').value = parseInt(saldo);
        document.getElementById('monto').max = parseInt(saldo);
        
        infoCard.style.display = 'block';
    } else {
        infoCard.style.display = 'none';
    }
});
</script>
@endpush
@endsection
