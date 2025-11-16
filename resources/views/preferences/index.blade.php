@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="mb-3 mb-sm-0">Préférences des invités</h5>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="input-group preference-search-group">
                            <span class="input-group-text bg-white border-end-0"><i class="ti ti-search"></i></span>
                            <input type="search" id="preference-search" class="form-control border-start-0"
                                placeholder="Rechercher (invité, boisson, table, note)..." autocomplete="off">
                            <span class="input-group-text bg-transparent border-start-0 d-none" id="preference-search-loader">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            </span>
                        </div>
                        <a href="{{ route('preferences.export') }}" class="btn btn-outline-success" title="Exporter les statistiques">
                            <i class="ti ti-download"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div id="preference-summary-container" class="mb-4">
                    @include('preferences.partials.summary', ['preferences' => $preferences, 'summary' => $summary])
                </div>

                <div id="preference-table-container">
                    @include('preferences.partials.table', ['preferences' => $preferences, 'summary' => $summary])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('preference-search');
        const tableContainer = document.getElementById('preference-table-container');
        const summaryContainer = document.getElementById('preference-summary-container');
        const loader = document.getElementById('preference-search-loader');
        const endpoint = '{{ route('preferences.search') }}';
        let debounceTimer = null;
        let activeController = null;

        function toggleLoader(visible) {
            if (!loader) return;
            loader.classList.toggle('d-none', !visible);
        }

        function fetchPreferences(query) {
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
                    tableContainer.innerHTML = data.table_html;
                    summaryContainer.innerHTML = data.summary_html;
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
                    fetchPreferences(query);
                }, 300);
            });
        }
    })();
</script>
@endpush
