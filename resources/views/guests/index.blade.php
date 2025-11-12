@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="mb-3 mb-sm-0">Invités</h5>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="input-group guest-search-group">
                            <span class="input-group-text bg-white border-end-0"><i class="ti ti-search"></i></span>
                            <input type="search" id="guest-search" class="form-control border-start-0"
                                placeholder="Rechercher un invité (nom, téléphone, email, table)..." autocomplete="off">
                            <span class="input-group-text bg-transparent border-start-0 d-none" id="guest-search-loader">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            </span>
                        </div>
                        <a href="{{ route('guests.create') }}" class="btn btn-primary">
                            <i class="ti ti-user-plus me-2"></i> Ajouter un invité
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
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div id="guest-table-container">
                    @include('guests.partials.table', ['guests' => $guests])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('guest-search');
        const resultsContainer = document.getElementById('guest-table-container');
        const loader = document.getElementById('guest-search-loader');
        const endpoint = '{{ route('guests.search') }}';
        let debounceTimer = null;
        let activeController = null;

        function toggleLoader(visible) {
            if (!loader) return;
            loader.classList.toggle('d-none', !visible);
        }

        function fetchGuests(query) {
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
                    fetchGuests(query);
                }, 300);
            });
        }
    })();
</script>
@endpush
