@extends('layouts.app')

@section('title', 'Suscriptores')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people me-2"></i>Suscriptores</h2>
    <a href="{{ route('subscribers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Suscriptor
    </a>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('subscribers.index') }}" method="GET" class="row g-2">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar por matrícula, documento o nombre..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
                @if(request('search'))
                    <a href="{{ route('subscribers.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Documento</th>
                        <th>Nombre Completo</th>
                        <th>Dirección</th>
                        <th>Sector</th>
                        <th>Estrato</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $subscriber)
                    <tr>
                        <td><strong>{{ $subscriber->matricula }}</strong></td>
                        <td>{{ $subscriber->documento }}</td>
                        <td>{{ $subscriber->full_name }}</td>
                        <td>{{ $subscriber->direccion }}</td>
                        <td>{{ $subscriber->sector }}</td>
                        <td>{{ $subscriber->estrato }}</td>
                        <td>
                            @if($subscriber->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('subscribers.show', $subscriber) }}" class="btn btn-outline-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('subscribers.edit', $subscriber) }}" class="btn btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('readings.create', ['subscriber_id' => $subscriber->id]) }}" class="btn btn-outline-success" title="Nueva Lectura">
                                    <i class="bi bi-speedometer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No se encontraron suscriptores
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($subscribers->hasPages())
    <div class="card-footer">
        {{ $subscribers->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
