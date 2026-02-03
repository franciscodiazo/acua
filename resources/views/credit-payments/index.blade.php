@extends('layouts.app')

@section('title', 'Abonos a Créditos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-credit-card me-2"></i>Abonos a Créditos/Deudas</h2>
    <a href="{{ route('credit-payments.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Abono
    </a>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('credit-payments.index') }}" method="GET" class="row g-2">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar por recibo o suscriptor..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
                @if(request('search'))
                    <a href="{{ route('credit-payments.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Matrícula</th>
                        <th>Suscriptor</th>
                        <th>Crédito/Deuda</th>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td><strong>{{ $payment->numero_recibo }}</strong></td>
                        <td>{{ $payment->subscriber->matricula }}</td>
                        <td>{{ $payment->subscriber->full_name }}</td>
                        <td>{{ $payment->credit->numero ?? '-' }}</td>
                        <td>{{ $payment->fecha->format('d/m/Y') }}</td>
                        <td>{{ \App\Models\CreditPayment::$metodosPago[$payment->metodo_pago] ?? ucfirst($payment->metodo_pago) }}</td>
                        <td class="text-success"><strong>${{ number_format($payment->monto, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="badge badge-{{ $payment->estado }}">{{ ucfirst($payment->estado) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('credit-payments.show', $payment) }}" class="btn btn-outline-primary" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('credit-payments.print', $payment) }}" class="btn btn-outline-secondary" target="_blank" title="Imprimir">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No hay abonos registrados</td>
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
