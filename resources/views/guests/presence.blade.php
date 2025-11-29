@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0">Présence des invités</h5>
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                        <div class="input-group guest-search-group" style="min-width: 250px; max-width: 500px;">
                            <span class="input-group-text bg-white border-end-0"><i class="ti ti-search"></i></span>
                            <input type="search" id="presence-search" class="form-control border-start-0"
                                placeholder="Rechercher un invité (nom, téléphone)..." autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3 px-3 px-md-4">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <label for="filter-table" class="form-label mb-2">Filtrer par table</label>
                        <select name="table_id" id="filter-table" class="form-select">
                            <option value="">Toutes les tables</option>
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}">{{ $table->name }}</option>
                            @endforeach
                            <option value="no_table">Non assigné</option>
                        </select>
                    </div>
                </div>

                <div id="presence-results">
                    @foreach ($guestsByTable as $tableGroup)
                        <div class="table-group mb-4" data-table-id="{{ $tableGroup['table_id'] }}" data-table-name="{{ strtolower($tableGroup['table']) }}">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <h6 class="mb-0 fw-bold text-primary">
                                    <i class="ti ti-table me-2"></i>{{ $tableGroup['table'] }}
                                </h6>
                                <span class="badge bg-light-primary text-primary">
                                    {{ count($tableGroup['guests']) }} invité(s)
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;" class="text-center">N°</th>
                                            <th>Invité(s)</th>
                                            <th class="d-none d-md-table-cell">Type</th>
                                            <th class="d-none d-lg-table-cell">Téléphone</th>
                                            <th class="text-center">Statut</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tableGroup['guests'] as $guest)
                                            <tr class="guest-row" 
                                                data-guest-id="{{ $guest->id }}"
                                                data-guest-name="{{ strtolower($guest->display_name) }}"
                                                data-guest-phone="{{ strtolower($guest->phone) }}"
                                                data-table-id="{{ $tableGroup['table_id'] }}"
                                                data-table-name="{{ strtolower($tableGroup['table']) }}">
                                                <td class="text-center text-muted fw-semibold">
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ $guest->display_name }}</span>
                                                        <div class="d-flex d-md-none flex-wrap gap-1 mt-1">
                                                            @if ($guest->type === 'couple')
                                                                <span class="badge bg-light-primary border border-primary text-primary text-capitalize small">
                                                                    <i class="ti ti-users"></i> Couple
                                                                </span>
                                                            @else
                                                                <span class="badge bg-light-primary border border-primary text-primary text-capitalize small">
                                                                    <i class="ti ti-user"></i> Solo
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    @if ($guest->type === 'couple')
                                                        <span class="badge bg-light-primary border border-primary text-primary text-capitalize">
                                                            <i class="ti ti-users me-1"></i> Couple
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light-primary border border-primary text-primary text-capitalize">
                                                            <i class="ti ti-user me-1"></i> Solo
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="d-none d-lg-table-cell">
                                                    <i class="ti ti-phone me-1"></i>{{ $guest->phone }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($guest->arrived_at)
                                                        <span class="badge bg-success">
                                                            <i class="ti ti-check me-1"></i> Présent
                                                        </span>
                                                        <div class="text-success small mt-1">
                                                            <i class="ti ti-clock me-1"></i>{{ $guest->arrived_at->format('H:i') }}
                                                        </div>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="ti ti-clock me-1"></i> En attente
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if ($guest->arrived_at)
                                                        <span class="text-muted small">
                                                            Déjà marqué
                                                        </span>
                                                    @else
                                                        <button type="button" 
                                                                class="btn btn-sm btn-success mark-arrived-btn" 
                                                                data-guest-id="{{ $guest->id }}"
                                                                data-guest-name="{{ $guest->display_name }}">
                                                            <i class="ti ti-check me-1"></i> Marquer présent
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="no-results" class="text-center text-muted py-5 d-none">
                    <i class="ti ti-search-off fs-1 mb-3"></i>
                    <p class="mb-0">Aucun invité trouvé.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('presence-search');
    const tableFilter = document.getElementById('filter-table');
    const guestRows = document.querySelectorAll('.guest-row');
    const tableGroups = document.querySelectorAll('.table-group');
    const noResults = document.getElementById('no-results');

    // Fonction de filtrage
    function performFilter() {
        const searchQuery = searchInput.value.toLowerCase().trim();
        const selectedTableId = tableFilter.value;
        let hasResults = false;

        guestRows.forEach(row => {
            const guestName = row.getAttribute('data-guest-name');
            const guestPhone = row.getAttribute('data-guest-phone');
            const tableId = row.getAttribute('data-table-id');
            const tableName = row.getAttribute('data-table-name');

            // Vérifier le filtre de recherche
            const matchesSearch = searchQuery === '' || 
                guestName.includes(searchQuery) || 
                guestPhone.includes(searchQuery);

            // Vérifier le filtre de table
            const matchesTable = selectedTableId === '' || 
                (selectedTableId === 'no_table' && tableId === 'no_table') ||
                (selectedTableId !== 'no_table' && tableId === selectedTableId);

            if (matchesSearch && matchesTable) {
                row.style.display = '';
                hasResults = true;
            } else {
                row.style.display = 'none';
            }
        });

        // Afficher/masquer les groupes de tables vides
        tableGroups.forEach(group => {
            const visibleRows = Array.from(group.querySelectorAll('.guest-row')).filter(row => {
                return row.style.display !== 'none';
            });
            if (visibleRows.length === 0) {
                group.style.display = 'none';
            } else {
                group.style.display = '';
                hasResults = true;
            }
        });

        // Afficher/masquer le message "Aucun résultat"
        if (hasResults || (searchQuery === '' && selectedTableId === '')) {
            noResults.classList.add('d-none');
        } else {
            noResults.classList.remove('d-none');
        }
    }

    // Écouter les changements dans la recherche et le filtre
    searchInput.addEventListener('input', performFilter);
    tableFilter.addEventListener('change', performFilter);

    // Gérer les boutons "Marquer présent"
    function initMarkArrivedButtons() {
        document.querySelectorAll('.mark-arrived-btn').forEach(btn => {
            // Éviter les doublons d'événements
            if (btn.hasAttribute('data-listener-attached')) {
                return;
            }
            btn.setAttribute('data-listener-attached', 'true');

            btn.addEventListener('click', function() {
                const button = this;
                const guestId = button.getAttribute('data-guest-id');
                const guestName = button.getAttribute('data-guest-name');
                const row = button.closest('tr');

                // Désactiver le bouton pendant la requête
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> En cours...';

                fetch(`/guests/${guestId}/mark-arrived`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Une erreur est survenue.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mettre à jour la ligne du tableau sans recharger la page
                        const statusCell = row.querySelector('td:nth-child(5)');
                        const actionCell = row.querySelector('td:nth-child(6)');
                        
                        // Mettre à jour le statut
                        statusCell.innerHTML = `
                            <span class="badge bg-success">
                                <i class="ti ti-check me-1"></i> Présent
                            </span>
                            <div class="text-success small mt-1">
                                <i class="ti ti-clock me-1"></i>${data.arrived_at}
                            </div>
                        `;
                        
                        // Mettre à jour l'action
                        actionCell.innerHTML = '<span class="text-muted small">Déjà marqué</span>';
                        
                        // Ajouter une classe pour indiquer que l'invité est présent
                        row.classList.add('table-success');
                    } else {
                        // Réactiver le bouton en cas d'erreur
                        button.disabled = false;
                        button.innerHTML = '<i class="ti ti-check me-1"></i> Marquer présent';
                        alert(data.message || 'Une erreur est survenue.');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    button.disabled = false;
                    button.innerHTML = '<i class="ti ti-check me-1"></i> Marquer présent';
                    alert(error.message || 'Une erreur est survenue lors du marquage de la présence.');
                });
            });
        });
    }

    // Initialiser les boutons au chargement
    initMarkArrivedButtons();
});
</script>
@endpush
