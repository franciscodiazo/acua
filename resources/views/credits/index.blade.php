@extends('layouts.app')

@section('title', 'Créditos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-credit-card me-2"></i>Créditos</h2>
    <a href="{{ route('credits.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Crédito
    </a>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('credits.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar suscriptor..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                    <option value="aplicado" {{ request('estado') == 'aplicado' ? 'selected' : '' }}>Aplicado</option>
                    <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tipo" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="credito" {{ request('tipo') == 'credito' ? 'selected' : '' }}>Crédito</option>
                    <option value="deuda" {{ request('tipo') == 'deuda' ? 'selected' : '' }}>Deuda</option>
                    <option value="cuota" {{ request('tipo') == 'cuota' ? 'selected' : '' }}>Cuota Pendiente</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                @if(request('search') || request('estado'))
                    <a href="{{ route('credits.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Suscriptor</th>
                        <th>Tipo</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Saldo</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($credits as $credit)
                    <tr>
                        <td>{{ $credit->numero }}</td>
                        <td>{{ $credit->subscriber->matricula }} - {{ $credit->subscriber->full_name }}</td>
                        <td>
                            @php
                                $tiposCredito = [
                                    'credito' => ['label' => 'Crédito', 'class' => 'bg-primary'],
                                    'deuda' => ['label' => 'Deuda', 'class' => 'bg-danger'],
                                    'cuota' => ['label' => 'Cuota Pendiente', 'class' => 'bg-warning text-dark']
                                ];
                                $tipo = $tiposCredito[$credit->tipo] ?? ['label' => 'Otro', 'class' => 'bg-secondary'];
                            @endphp
                            <span class="badge {{ $tipo['class'] }}">{{ $tipo['label'] }}</span>
                        </td>
                        <td>{{ $credit->concepto }}</td>
                        <td>${{ number_format($credit->monto, 0, ',', '.') }}</td>
                        <td class="{{ $credit->saldo > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                            ${{ number_format($credit->saldo, 0, ',', '.') }}
                        </td>
                        <td>{{ $credit->fecha->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $credit->estado }}">{{ ucfirst($credit->estado) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('credits.show', $credit) }}" class="btn btn-outline-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($credit->estado === 'activo' && $credit->saldo > 0)
                                <a href="{{ route('credit-payments.create', ['credit_id' => $credit->id]) }}" 
                                   class="btn btn-outline-success" title="Registrar Abono">
                                    <i class="bi bi-plus-circle"></i>
                                </a>
                                @endif
                                @if($credit->estado === 'activo')
                                <form action="{{ route('credits.anular', $credit) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de anular este crédito?')">
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
                        <td colspan="9" class="text-center text-muted py-4">
                            No se encontraron créditos
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($credits->hasPages())
    <div class="card-footer">
        {{ $credits->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
