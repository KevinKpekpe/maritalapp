@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0">Invités</h5>
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                        <div class="input-group guest-search-group grow" style="min-width: 250px; max-width: 500px;">
                            <span class="input-group-text bg-white border-end-0"><i class="ti ti-search"></i></span>
                            <input type="search" id="guest-search" class="form-control border-start-0"
                                placeholder="Rechercher un invité (nom, téléphone, email, table)..." autocomplete="off">
                            <span class="input-group-text bg-transparent border-start-0 d-none" id="guest-search-loader">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <a href="{{ route('guests.trash') }}" class="btn btn-outline-secondary" title="Corbeille">
                                <i class="ti ti-trash"></i>
                            </a>
                            <button type="button" id="send-selected-btn" class="btn btn-success d-none" disabled title="Envoyer à la sélection">
                                <i class="ti ti-brand-whatsapp"></i> <span id="selected-count">0</span>
                            </button>
                            <button type="button" id="confirm-selected-btn" class="btn btn-outline-success d-none" disabled title="Confirmer la sélection">
                                <i class="ti ti-check"></i> <span id="confirm-count">0</span>
                            </button>
                            <a href="{{ route('guests.export') }}" class="btn btn-outline-success" title="Exporter">
                                <i class="ti ti-download"></i>
                            </a>
                            <a href="{{ route('guests.import.show') }}" class="btn btn-outline-info" title="Importer">
                                <i class="ti ti-upload"></i>
                            </a>
                            <a href="{{ route('guests.create') }}" class="btn btn-primary" title="Ajouter un invité">
                                <i class="ti ti-user-plus"></i>
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

                <div class="row g-4 mb-4 mx-0" id="guest-filters">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="filter-rsvp-status" class="form-label mb-2">Statut RSVP</label>
                        <select name="rsvp_status" id="filter-rsvp-status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="not_confirmed">Non confirmés</option>
                            <option value="confirmed">Confirmés</option>
                            <option value="declined">Déclinés</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="filter-whatsapp-status" class="form-label mb-2">Lien WhatsApp</label>
                        <select name="whatsapp_status" id="filter-whatsapp-status" class="form-select">
                            <option value="">Tous les invités</option>
                            <option value="not_sent">Lien non envoyé</option>
                            <option value="sent">Lien envoyé</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="filter-guest-type" class="form-label mb-2">Type d'invité</label>
                        <select name="guest_type" id="filter-guest-type" class="form-select">
                            <option value="">Tous les types</option>
                            <option value="solo">Solo</option>
                            <option value="couple">Couple</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="filter-table" class="form-label mb-2">Table</label>
                        <select name="table_id" id="filter-table" class="form-select">
                            <option value="">Toutes les tables</option>
                            @foreach ($tables ?? [] as $table)
                                <option value="{{ $table->id }}">{{ $table->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="filter-sort" class="form-label mb-2">Tri</label>
                        <select name="sort" id="filter-sort" class="form-select">
                            <option value="recent" selected>Du plus récent au plus ancien</option>
                            <option value="oldest">Du plus ancien au plus récent</option>
                            <option value="table">Par table</option>
                            <option value="">Tri alphabétique</option>
                        </select>
                    </div>
                </div>

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
        const filtersForm = document.getElementById('guest-filters');
        const filterInputs = filtersForm ? filtersForm.querySelectorAll('select') : [];
        const endpoint = '{{ route('guests.search') }}';
        let debounceTimer = null;
        let activeController = null;
        const MAX_SELECTION = 100;

        function toggleLoader(visible) {
            if (!loader) return;
            loader.classList.toggle('d-none', !visible);
        }

        function updateURL() {
            const url = new URL(window.location);
            const query = searchInput ? searchInput.value.trim() : '';
            
            // Réinitialiser les paramètres
            url.searchParams.delete('query');
            url.searchParams.delete('rsvp_status');
            url.searchParams.delete('whatsapp_status');
            url.searchParams.delete('guest_type');
            url.searchParams.delete('table_id');
            url.searchParams.delete('sort');

            // Ajouter les nouveaux paramètres
            if (query) {
                url.searchParams.set('query', query);
            }

            filterInputs.forEach(select => {
                if (select.value) {
                    url.searchParams.set(select.name, select.value);
                }
            });

            // Mettre à jour l'URL sans recharger la page
            window.history.pushState({}, '', url);
        }

        function restoreFiltersFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Restaurer la recherche
            if (searchInput && urlParams.has('query')) {
                searchInput.value = urlParams.get('query');
            }

            // Restaurer les filtres
            filterInputs.forEach(select => {
                const paramValue = urlParams.get(select.name);
                if (paramValue !== null) {
                    select.value = paramValue;
                }
            });
        }

        function fetchGuests() {
            if (activeController) {
                activeController.abort();
            }
            activeController = new AbortController();
            const signal = activeController.signal;

            const url = new URL(endpoint, window.location.origin);
            const query = searchInput ? searchInput.value.trim() : '';
            if (query) {
                url.searchParams.set('query', query);
            }

            filterInputs.forEach(select => {
                if (select.value) {
                    url.searchParams.set(select.name, select.value);
                }
            });

            // Mettre à jour l'URL
            updateURL();

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
            const confirmBtn = document.getElementById('confirm-selected-btn');
            const confirmCountSpan = document.getElementById('confirm-count');
            const selectAll = document.getElementById('select-all-guests');

            const count = checked.length;
            
            // Compter les invités en attente de confirmation parmi les sélectionnés
            const pendingChecked = Array.from(checked).filter(cb => {
                const rsvpStatus = cb.getAttribute('data-rsvp-status');
                return !rsvpStatus || rsvpStatus === 'pending';
            });
            const pendingCount = pendingChecked.length;

            // Gérer le bouton d'envoi WhatsApp
            if (count > 0) {
                sendBtn.classList.remove('d-none');
                sendBtn.disabled = false;
                countSpan.textContent = count;
            } else {
                sendBtn.classList.add('d-none');
                sendBtn.disabled = true;
            }

            // Gérer le bouton de confirmation
            if (pendingCount > 0) {
                confirmBtn.classList.remove('d-none');
                confirmBtn.disabled = false;
                confirmCountSpan.textContent = pendingCount;
            } else {
                confirmBtn.classList.add('d-none');
                confirmBtn.disabled = true;
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

                    const guestCount = guestIds.length;
                    const guestText = guestCount > 1 ? 'invités' : 'invité';

                    if (window.showConfirmModal) {
                        window.showConfirmModal({
                            title: 'Envoyer les invitations WhatsApp',
                            message: `Êtes-vous sûr de vouloir envoyer les invitations WhatsApp à ${guestCount} ${guestText} sélectionné${guestCount > 1 ? 's' : ''} ?`,
                            confirmText: 'Envoyer',
                            confirmClass: 'btn-success',
                            icon: 'ti-brand-whatsapp',
                            iconColor: 'text-success',
                            onSubmit: function() {
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
                            }
                        });
                    } else {
                        // Fallback si le modal n'est pas disponible
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
                    }
                });
            }

            // Bouton de confirmation en masse
            const confirmBtn = document.getElementById('confirm-selected-btn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    const checked = document.querySelectorAll('.guest-checkbox:checked');
                    const pendingChecked = Array.from(checked).filter(cb => {
                        const rsvpStatus = cb.getAttribute('data-rsvp-status');
                        return !rsvpStatus || rsvpStatus === 'pending';
                    });
                    const guestIds = Array.from(pendingChecked).map(cb => cb.value);

                    if (guestIds.length === 0) {
                        alert('Veuillez sélectionner au moins un invité en attente de confirmation.');
                        return;
                    }

                    if (guestIds.length > MAX_SELECTION) {
                        alert('Vous ne pouvez sélectionner que ' + MAX_SELECTION + ' invités maximum.');
                        return;
                    }

                    const guestCount = guestIds.length;
                    const guestText = guestCount > 1 ? 'invités' : 'invité';

                    if (window.showConfirmModal) {
                        window.showConfirmModal({
                            title: 'Confirmer les invitations',
                            message: `Êtes-vous sûr de vouloir confirmer manuellement ${guestCount} ${guestText} en attente de confirmation ?`,
                            confirmText: 'Confirmer',
                            confirmClass: 'btn-success',
                            icon: 'ti-check',
                            iconColor: 'text-success',
                            onSubmit: function() {
                                // Désactiver le bouton pendant la confirmation
                                confirmBtn.disabled = true;
                                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Confirmation en cours...';

                                // Créer un formulaire et le soumettre
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = '{{ route('guests.confirm_bulk') }}';

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
                            }
                        });
                    } else {
                        // Fallback si le modal n'est pas disponible
                        if (!confirm('Confirmer ' + guestIds.length + ' invitation(s) en attente ?')) {
                            return;
                        }

                        // Désactiver le bouton pendant la confirmation
                        confirmBtn.disabled = true;
                        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Confirmation en cours...';

                        // Créer un formulaire et le soumettre
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('guests.confirm_bulk') }}';

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
                    }
                });
            }

            updateSelectionUI();
        }

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchGuests();
                }, 300);
            });
        }

        filterInputs.forEach(select => {
            select.addEventListener('change', () => {
                fetchGuests();
            });
        });

        // Restaurer les filtres depuis l'URL au chargement
        restoreFiltersFromURL();

        // Si des filtres sont présents dans l'URL, charger les invités
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.toString()) {
            fetchGuests();
        }

        // Initialiser les handlers au chargement de la page
        initSelectionHandlers();
    })();
</script>
@endpush
