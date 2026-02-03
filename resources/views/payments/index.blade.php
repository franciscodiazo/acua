@extends('layouts.app')

@section('title', 'Pagos y Abonos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cash-coin me-2"></i>Pagos y Abonos</h2>
    <a href="{{ route('payments.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Pago
    </a>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('payments.index') }}" method="GET" class="row g-2">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar recibo o suscriptor..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
                @if(request('search'))
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Recibo</th>
                        <th>Factura</th>
                        <th>Suscriptor</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td><strong>{{ $payment->numero_recibo }}</strong></td>
                        <td>
                            <a href="{{ route('invoices.show', $payment->invoice) }}">
                                {{ $payment->invoice->numero }}
                            </a>
                        </td>
                        <td>{{ $payment->subscriber->full_name }}</td>
                        <td>{{ $payment->fecha->format('d/m/Y') }}</td>
                        <td>${{ number_format($payment->monto, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($payment->metodo_pago) }}</td>
                        <td>
                            <span class="badge badge-{{ $payment->estado }}">{{ ucfirst($payment->estado) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($payment->estado === 'activo')
                                <form action="{{ route('payments.anular', $payment) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de anular este pago?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger" title="Anular">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No se encontraron pagos
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer">
        {{ $payments->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
