@extends('layouts.app')

@section('title', 'Cuotas Familiares')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt me-2"></i>Cuotas Familiares</h2>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('invoices.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar cuota o suscriptor..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="ciclo" class="form-select">
                    <option value="">Todos los ciclos</option>
                    @foreach($ciclos as $ciclo)
                        <option value="{{ $ciclo }}" {{ request('ciclo') == $ciclo ? 'selected' : '' }}>
                            {{ $ciclo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="pagada" {{ request('estado') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                    <option value="parcial" {{ request('estado') == 'parcial' ? 'selected' : '' }}>Parcial</option>
                    <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anulada</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                @if(request('search') || request('ciclo') || request('estado'))
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Matrícula</th>
                        <th>Suscriptor</th>
                        <th>Ciclo</th>
                        <th>Fecha Emisión</th>
                        <th>Vencimiento</th>
                        <th>Total</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td><strong>{{ $invoice->numero }}</strong></td>
                        <td>{{ $invoice->subscriber->matricula }}</td>
                        <td>{{ $invoice->subscriber->full_name }}</td>
                        <td>{{ $invoice->ciclo }}</td>
                        <td>{{ $invoice->fecha_emision->format('d/m/Y') }}</td>
                        <td>{{ $invoice->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td>${{ number_format($invoice->total, 0, ',', '.') }}</td>
                        <td>${{ number_format($invoice->saldo, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-{{ $invoice->estado }}">{{ ucfirst($invoice->estado) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-outline-secondary" title="Imprimir" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                                @if(in_array($invoice->estado, ['pendiente', 'parcial']))
                                <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-outline-success" title="Pagar">
                                    <i class="bi bi-cash-coin"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            No se encontraron facturas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer">
        {{ $invoices->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
