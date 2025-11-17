@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0">Liste des tables</h5>
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                        <div class="input-group table-search-group grow" style="min-width: 250px; max-width: 500px;">
                            <span class="input-group-text bg-white border-end-0"><i class="ti ti-search"></i></span>
                            <input type="search" id="table-search" class="form-control border-start-0"
                                placeholder="Rechercher une table (nom, description)..." autocomplete="off">
                            <span class="input-group-text bg-transparent border-start-0 d-none" id="table-search-loader">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <a href="{{ route('tables.trash') }}" class="btn btn-outline-secondary" title="Corbeille">
                                <i class="ti ti-trash"></i>
                            </a>
                            <a href="{{ route('tables.export') }}" class="btn btn-outline-success" title="Exporter">
                                <i class="ti ti-download"></i>
                            </a>
                            <a href="{{ route('tables.import.show') }}" class="btn btn-outline-info" title="Importer">
                                <i class="ti ti-upload"></i>
                            </a>
                            <a href="{{ route('tables.create') }}" class="btn btn-primary" title="Ajouter une table">
                                <i class="ti ti-layout-grid-add"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3 px-3 px-md-4">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('import_errors') && count(session('import_errors')) > 0)
                    <div class="alert alert-warning" role="alert">
                        <strong>Erreurs d'import :</strong>
                        <ul class="mb-0 mt-2">
                            @foreach (session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="table-table-container">
                    @include('tables.partials.table', ['tables' => $tables])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('table-search');
        const resultsContainer = document.getElementById('table-table-container');
        const loader = document.getElementById('table-search-loader');
        const endpoint = '{{ route('tables.search') }}';
        let debounceTimer = null;
        let activeController = null;

        function toggleLoader(visible) {
            if (!loader) return;
            loader.classList.toggle('d-none', !visible);
        }

        function fetchTables(query) {
            if (activeController) {
                activeController.abort();
            }
            activeController = new AbortController();
            const signal = activeController.signal;

            const url = new URL(endpoint, window.location.origin);
            if (query) {
                url.searchParams.set('query', query);
            }

            toggleLoader(true);

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                signal
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    resultsContainer.innerHTML = data.html;
                })
                .catch(error => {
                    if (error.name !== 'AbortError') {
                        console.error(error);
                    }
                })
                .finally(() => {
                    toggleLoader(false);
                });
        }

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchTables(query);
                }, 300);
            });
        }
    })();
</script>
@endpush
