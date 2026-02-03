@extends('layouts.app')

@section('title', 'Nuevo Pago')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cash-coin me-2"></i>Registrar Pago</h2>
    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Factura <span class="text-danger">*</span></label>
                            <select name="invoice_id" id="invoice_id" class="form-select @error('invoice_id') is-invalid @enderror" required>
                                <option value="">Seleccione una factura...</option>
                                @foreach($invoices as $inv)
                                    <option value="{{ $inv->id }}" 
                                            data-saldo="{{ $inv->saldo }}"
                                            data-total="{{ $inv->total }}"
                                            {{ old('invoice_id', $invoice?->id) == $inv->id ? 'selected' : '' }}>
                                        {{ $inv->numero }} - {{ $inv->subscriber->matricula }} - {{ $inv->subscriber->full_name }} 
                                        (Saldo: ${{ number_format($inv->saldo, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('invoice_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Monto a Pagar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="monto" id="monto" 
                                       class="form-control @error('monto') is-invalid @enderror" 
                                       value="{{ old('monto', $invoice?->saldo) }}" min="1" step="1" required>
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
                                <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="otro" {{ old('metodo_pago') == 'otro' ? 'selected' : '' }}>Otro</option>
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
                            <i class="bi bi-check-lg me-1"></i> Registrar Pago
                        </button>
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card bg-light" id="infoFactura" style="{{ $invoice ? '' : 'display:none' }}">
            <div class="card-header">
                <i class="bi bi-receipt me-1"></i> Información de Factura
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="small text-muted">Total Factura</label>
                    <p class="mb-0 fs-5" id="totalFactura">
                        ${{ $invoice ? number_format($invoice->total, 0, ',', '.') : '0' }}
                    </p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted">Saldo Pendiente</label>
                    <p class="mb-0 fs-4 text-danger fw-bold" id="saldoFactura">
                        ${{ $invoice ? number_format($invoice->saldo, 0, ',', '.') : '0' }}
                    </p>
                </div>
                <hr>
                <div>
                    <label class="small text-muted">Nuevo Saldo</label>
                    <p class="mb-0 fs-3 fw-bold text-success" id="nuevoSaldo">$0</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function actualizarInfo() {
        const option = $('#invoice_id option:selected');
        const saldo = parseFloat(option.data('saldo')) || 0;
        const total = parseFloat(option.data('total')) || 0;
        const monto = parseFloat($('#monto').val()) || 0;
        
        if (option.val()) {
            $('#infoFactura').show();
            $('#totalFactura').text('$' + total.toLocaleString('es-CO'));
            $('#saldoFactura').text('$' + saldo.toLocaleString('es-CO'));
            
            const nuevoSaldo = Math.max(0, saldo - monto);
            $('#nuevoSaldo').text('$' + nuevoSaldo.toLocaleString('es-CO'));
            
            if (nuevoSaldo === 0) {
                $('#nuevoSaldo').removeClass('text-success').addClass('text-primary');
            } else {
                $('#nuevoSaldo').removeClass('text-primary').addClass('text-success');
            }
        } else {
            $('#infoFactura').hide();
        }
    }
    
    $('#invoice_id').change(function() {
        const saldo = parseFloat($(this).find('option:selected').data('saldo')) || 0;
        $('#monto').val(Math.round(saldo)).attr('max', Math.round(saldo));
        actualizarInfo();
    });
    
    $('#monto').on('input', actualizarInfo);
    
    actualizarInfo();
});
</script>
@endpush
