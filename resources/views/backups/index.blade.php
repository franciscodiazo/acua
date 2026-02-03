@extends('layouts.app')

@section('title', 'Copias de Respaldo')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-cloud-download text-primary"></i> Copias de Respaldo
            </h1>
            <p class="text-muted">Exportar e importar datos del sistema</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('import_errors') && count(session('import_errors')) > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle"></i> Errores durante la importación:</strong>
        <ul class="mb-0 mt-2">
            @foreach(array_slice(session('import_errors'), 0, 10) as $error)
            <li>{{ $error }}</li>
            @endforeach
            @if(count(session('import_errors')) > 10)
            <li>... y {{ count(session('import_errors')) - 10 }} errores más</li>
            @endif
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Estadísticas actuales -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center border-primary">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-primary">{{ number_format($stats['subscribers']) }}</h4>
                    <small class="text-muted">Suscriptores</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-info">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-info">{{ number_format($stats['readings']) }}</h4>
                    <small class="text-muted">Lecturas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-success">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-success">{{ number_format($stats['invoices']) }}</h4>
                    <small class="text-muted">Facturas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-warning">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-warning">{{ number_format($stats['credits']) }}</h4>
                    <small class="text-muted">Créditos</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-secondary">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-secondary">{{ number_format($stats['payments']) }}</h4>
                    <small class="text-muted">Pagos</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-dark">
                <div class="card-body py-3">
                    <h4 class="mb-0">{{ number_format($stats['credit_payments']) }}</h4>
                    <small class="text-muted">Abonos</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- BACKUP COMPLETO -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-database-down"></i> Backup Completo de Base de Datos</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Descarga un archivo SQL con toda la estructura y datos de la base de datos.
                        Este archivo puede ser usado para restaurar completamente el sistema.
                    </p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Incluye:</strong> Empresa, Tarifas, Suscriptores, Lecturas, Facturas, Pagos, Créditos y Abonos.
                    </div>
                    <a href="{{ route('backups.export-database') }}" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-download"></i> Descargar Backup SQL
                    </a>
                </div>
            </div>
        </div>

        <!-- EXPORTAR DATOS INDIVIDUALES -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-spreadsheet"></i> Exportar Datos (CSV)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Descarga archivos CSV individuales para cada tabla. Útiles para análisis en Excel o Google Sheets.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('backups.export-subscribers') }}" class="btn btn-outline-success">
                            <i class="bi bi-people"></i> Exportar Suscriptores ({{ $stats['subscribers'] }})
                        </a>
                        <a href="{{ route('backups.export-readings') }}" class="btn btn-outline-success">
                            <i class="bi bi-speedometer2"></i> Exportar Lecturas ({{ $stats['readings'] }})
                        </a>
                        <a href="{{ route('backups.export-credits') }}" class="btn btn-outline-success">
                            <i class="bi bi-credit-card"></i> Exportar Créditos ({{ $stats['credits'] }})
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- IMPORTAR SUSCRIPTORES -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Importar Suscriptores</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Importa suscriptores desde un archivo CSV. Si la matrícula ya existe, se actualizan los datos.
                    </p>
                    <a href="{{ route('backups.template-subscribers') }}" class="btn btn-sm btn-outline-secondary mb-3">
                        <i class="bi bi-file-earmark-arrow-down"></i> Descargar Plantilla
                    </a>
                    <form action="{{ route('backups.import-subscribers') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" class="form-control form-control-sm" name="file" accept=".csv,.txt" required>
                        </div>
                        <button type="submit" class="btn btn-info w-100" onclick="return confirm('¿Importar suscriptores? Los existentes con la misma matrícula serán actualizados.')">
                            <i class="bi bi-upload"></i> Importar CSV
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- IMPORTAR LECTURAS -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-speedometer2"></i> Importar Lecturas</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Importa lecturas de medidores desde CSV. Los suscriptores deben existir previamente (por matrícula).
                    </p>
                    <a href="{{ route('backups.template-readings') }}" class="btn btn-sm btn-outline-secondary mb-3">
                        <i class="bi bi-file-earmark-arrow-down"></i> Descargar Plantilla
                    </a>
                    <form action="{{ route('backups.import-readings') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" class="form-control form-control-sm" name="file" accept=".csv,.txt" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100" onclick="return confirm('¿Importar lecturas? Se verificará que los suscriptores existan.')">
                            <i class="bi bi-upload"></i> Importar CSV
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- IMPORTAR CRÉDITOS -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card"></i> Importar Créditos</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Importa créditos/deudas desde CSV. Los suscriptores deben existir previamente (por matrícula).
                    </p>
                    <a href="{{ route('backups.template-credits') }}" class="btn btn-sm btn-outline-secondary mb-3">
                        <i class="bi bi-file-earmark-arrow-down"></i> Descargar Plantilla
                    </a>
                    <form action="{{ route('backups.import-credits') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" class="form-control form-control-sm" name="file" accept=".csv,.txt" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Importar créditos? Se verificará que los suscriptores existan.')">
                            <i class="bi bi-upload"></i> Importar CSV
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Instrucciones -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-question-circle"></i> Instrucciones de Uso</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h6 class="text-primary"><i class="bi bi-1-circle"></i> Backup SQL</h6>
                    <p class="small text-muted">
                        El archivo SQL contiene toda la base de datos. Para restaurar, usa phpMyAdmin o un cliente MySQL 
                        y ejecuta el archivo SQL en una base de datos vacía.
                    </p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-success"><i class="bi bi-2-circle"></i> Exportar CSV</h6>
                    <p class="small text-muted">
                        Los archivos CSV se pueden abrir en Excel. Útil para generar reportes personalizados o 
                        migrar datos a otro sistema.
                    </p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-info"><i class="bi bi-3-circle"></i> Importar CSV</h6>
                    <p class="small text-muted">
                        Descarga primero la plantilla, llénala con tus datos en Excel y guárdala como CSV (UTF-8). 
                        Luego súbela al sistema.
                    </p>
                </div>
            </div>
            <hr>
            <div class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Importante:</strong> Se recomienda realizar backups periódicos (mínimo semanales) y guardar las copias en un lugar seguro diferente al servidor.
            </div>
        </div>
    </div>
</div>
@endsection
