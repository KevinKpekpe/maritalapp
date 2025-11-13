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
                        <button type="button" id="send-selected-btn" class="btn btn-success d-none" disabled>
                            <i class="ti ti-brand-whatsapp me-2"></i> Envoyer à la sélection (<span id="selected-count">0</span>)
                        </button>
                        <a href="{{ route('guests.export') }}" class="btn btn-outline-success">
                            <i class="ti ti-download me-2"></i> Exporter
                        </a>
                        <a href="{{ route('guests.import.show') }}" class="btn btn-outline-info">
                            <i class="ti ti-upload me-2"></i> Importer
                        </a>
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
                @if (session('bulk_errors') && count(session('bulk_errors')) > 0)
                    <div class="alert alert-warning" role="alert">
                        <strong>Erreurs d'envoi :</strong>
                        <ul class="mb-0 mt-2">
                            @foreach (session('bulk_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
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
        const MAX_SELECTION = 100;

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
                    initSelectionHandlers();
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

        function updateSelectionUI() {
            const checkboxes = document.querySelectorAll('.guest-checkbox:not(:disabled)');
            const checked = document.querySelectorAll('.guest-checkbox:checked');
            const sendBtn = document.getElementById('send-selected-btn');
            const countSpan = document.getElementById('selected-count');
            const selectAll = document.getElementById('select-all-guests');

            const count = checked.length;

            if (count > 0) {
                sendBtn.classList.remove('d-none');
                sendBtn.disabled = false;
                countSpan.textContent = count;
            } else {
                sendBtn.classList.add('d-none');
                sendBtn.disabled = true;
            }

            if (selectAll) {
                if (count === 0) {
                    selectAll.indeterminate = false;
                    selectAll.checked = false;
                } else if (count === checkboxes.length) {
                    selectAll.indeterminate = false;
                    selectAll.checked = true;
                } else {
                    selectAll.indeterminate = true;
                }
            }
        }

        function initSelectionHandlers() {
            const selectAll = document.getElementById('select-all-guests');
            const checkboxes = document.querySelectorAll('.guest-checkbox');
            const sendBtn = document.getElementById('send-selected-btn');

            // Sélectionner/désélectionner tout
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    const enabledCheckboxes = Array.from(checkboxes).filter(cb => !cb.disabled);
                    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked && !cb.disabled).length;

                    if (this.checked) {
                        // Sélectionner jusqu'à la limite
                        let selected = 0;
                        enabledCheckboxes.forEach(cb => {
                            if (selected < MAX_SELECTION) {
                                cb.checked = true;
                                selected++;
                            }
                        });
                        if (selected >= MAX_SELECTION) {
                            alert('Limite de ' + MAX_SELECTION + ' invités atteinte.');
                        }
                    } else {
                        enabledCheckboxes.forEach(cb => {
                            cb.checked = false;
                        });
                    }
                    updateSelectionUI();
                });
            }

            // Gérer la sélection individuelle
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checked = document.querySelectorAll('.guest-checkbox:checked').length;

                    if (this.checked && checked > MAX_SELECTION) {
                        this.checked = false;
                        alert('Vous ne pouvez sélectionner que ' + MAX_SELECTION + ' invités maximum.');
                        return;
                    }

                    updateSelectionUI();
                });
            });

            // Bouton d'envoi en masse
            if (sendBtn) {
                sendBtn.addEventListener('click', function() {
                    const checked = document.querySelectorAll('.guest-checkbox:checked');
                    const guestIds = Array.from(checked).map(cb => cb.value);

                    if (guestIds.length === 0) {
                        alert('Veuillez sélectionner au moins un invité.');
                        return;
                    }

                    if (guestIds.length > MAX_SELECTION) {
                        alert('Vous ne pouvez sélectionner que ' + MAX_SELECTION + ' invités maximum.');
                        return;
                    }

                    if (!confirm('Envoyer les invitations WhatsApp à ' + guestIds.length + ' invité(s) sélectionné(s) ?')) {
                        return;
                    }

                    // Désactiver le bouton pendant l'envoi
                    sendBtn.disabled = true;
                    sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Envoi en cours...';

                    // Créer un formulaire et le soumettre
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('guests.send_bulk_invitations') }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    guestIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'guest_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                });
            }

            updateSelectionUI();
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

        // Initialiser les handlers au chargement de la page
        initSelectionHandlers();
    })();
</script>
@endpush
