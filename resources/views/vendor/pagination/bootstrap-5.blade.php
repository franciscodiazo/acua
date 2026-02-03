@if ($paginator->hasPages())
    <nav class="pagination-wrapper" aria-label="Navegación de páginas">
        {{-- Vista móvil - Solo botones Anterior/Siguiente --}}
        <div class="d-flex justify-content-between align-items-center d-sm-none mb-3">
            <div class="pagination-info-mobile">
                <small class="text-muted">
                    <i class="bi bi-file-earmark-text"></i>
                    {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} de {{ $paginator->total() }}
                </small>
            </div>
            <div>
                @if ($paginator->onFirstPage())
                    <button class="btn btn-sm btn-outline-secondary" disabled>
                        <i class="bi bi-chevron-left"></i> Anterior
                    </button>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-chevron-left"></i> Anterior
                    </a>
                @endif
                
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-sm btn-primary ms-1">
                        Siguiente <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <button class="btn btn-sm btn-outline-secondary ms-1" disabled>
                        Siguiente <i class="bi bi-chevron-right"></i>
                    </button>
                @endif
            </div>
        </div>

        {{-- Vista desktop - Paginación completa --}}
        <div class="d-none d-sm-flex flex-column flex-md-row align-items-center justify-content-between">
            {{-- Información de registros --}}
            <div class="pagination-info mb-3 mb-md-0">
                <p class="mb-0 text-muted">
                    <i class="bi bi-info-circle text-primary"></i>
                    Mostrando 
                    <span class="fw-bold text-primary">{{ $paginator->firstItem() }}</span>
                    al
                    <span class="fw-bold text-primary">{{ $paginator->lastItem() }}</span>
                    de
                    <span class="fw-bold text-dark">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            {{-- Botones de paginación --}}
            <div class="pagination-buttons">
                <ul class="pagination pagination-custom mb-0">
                    {{-- Botón Primera Página --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="bi bi-chevron-double-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url(1) }}" title="Primera página">
                                <i class="bi bi-chevron-double-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Botón Anterior --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Página anterior">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Números de página --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled d-none d-md-block">
                                <span class="page-link">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link page-link-active">
                                            {{ $page }}
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item d-none d-md-block">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Botón Siguiente --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Página siguiente">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        </li>
                    @endif

                    {{-- Botón Última Página --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" title="Última página">
                                <i class="bi bi-chevron-double-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="bi bi-chevron-double-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Selector de páginas (opcional - solo en desktop) --}}
        @if ($paginator->lastPage() > 1)
        <div class="d-none d-lg-flex justify-content-center mt-3">
            <div class="input-group" style="max-width: 200px;">
                <span class="input-group-text bg-light">
                    <i class="bi bi-skip-forward"></i>
                </span>
                <select class="form-select form-select-sm" onchange="if(this.value) window.location.href=this.value">
                    <option value="">Ir a página...</option>
                    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                        <option value="{{ $paginator->url($i) }}" {{ $i == $paginator->currentPage() ? 'selected' : '' }}>
                            Página {{ $i }} de {{ $paginator->lastPage() }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>
        @endif
    </nav>

    {{-- Estilos personalizados --}}
    <style>
        .pagination-wrapper {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px solid #e9ecef;
        }
        
        .pagination-custom {
            gap: 0.25rem;
        }
        
        .pagination-custom .page-link {
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            color: #495057;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease;
            min-width: 40px;
            text-align: center;
        }
        
        .pagination-custom .page-link:hover {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.25);
        }
        
        .pagination-custom .page-item.active .page-link {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            border-color: #0d6efd;
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
            transform: scale(1.1);
        }
        
        .pagination-custom .page-item.disabled .page-link {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #adb5bd;
            cursor: not-allowed;
        }
        
        .pagination-info {
            font-size: 0.95rem;
        }
        
        .pagination-info-mobile {
            font-size: 0.875rem;
        }
        
        /* Animación para números de página */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .pagination-custom .page-item {
            animation: fadeIn 0.3s ease;
        }
        
        /* Responsivo */
        @media (max-width: 576px) {
            .pagination-custom .page-link {
                padding: 0.4rem 0.6rem;
                font-size: 0.875rem;
                min-width: 35px;
            }
        }
        
        @media (min-width: 768px) {
            .pagination-wrapper {
                margin-top: 2rem;
                padding-top: 1.5rem;
            }
        }
    </style>
@endif
