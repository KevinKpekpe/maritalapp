@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="mb-3 mb-sm-0">Boissons disponibles</h5>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="input-group beverage-search-group">
                            <span class="input-group-text bg-white border-end-0"><i class="ti ti-search"></i></span>
                            <input type="search" id="beverage-search" class="form-control border-start-0"
                                placeholder="Rechercher une boisson (nom, catégorie)..." autocomplete="off">
                            <span class="input-group-text bg-transparent border-start-0 d-none" id="beverage-search-loader">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            </span>
                        </div>
                        <a href="{{ route('beverages.create') }}" class="btn btn-primary" title="Ajouter une boisson">
                            <i class="ti ti-circle-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div id="beverage-table-container">
                    @include('beverages.partials.table', ['beverages' => $beverages])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('beverage-search');
        const resultsContainer = document.getElementById('beverage-table-container');
        const loader = document.getElementById('beverage-search-loader');
        const endpoint = '{{ route('beverages.search') }}';
        let debounceTimer = null;
        let activeController = null;

        function toggleLoader(visible) {
            if (!loader) return;
            loader.classList.toggle('d-none', !visible);
        }

        function fetchBeverages(query) {
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
                    fetchBeverages(query);
                }, 300);
            });
        }
    })();
</script>
@endpush
