@if ($guests->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="ti ti-trash me-2"></i> Aucun invité archivé.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Invité(s)</th>
                    <th>Type</th>
                    <th>Table</th>
                    <th>Téléphone</th>
                    <th>Statut RSVP</th>
                    <th>Créé le</th>
                    <th>Archivé le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guests as $guest)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $guest->display_name }}</span>
                        </td>
                        <td>
                            @if ($guest->type === 'couple')
                                <span class="badge bg-light-primary border border-primary text-primary text-capitalize">
                                    <i class="ti ti-users me-1"></i> Couple
                                </span>
                            @elseif ($guest->type === 'solo')
                                <span class="badge bg-light-primary border border-primary text-primary text-capitalize">
                                    <i class="ti ti-user me-1"></i> Solo
                                </span>
                            @else
                                <span class="badge bg-light-primary border border-primary text-primary text-capitalize">
                                    {{ $guest->type }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if ($guest->table)
                                {{ $guest->table->name }}
                            @else
                                <span class="text-muted">Non définie</span>
                            @endif
                        </td>
                        <td>{{ $guest->phone }}</td>
                        <td>
                            @php
                                $status = $guest->rsvp_status;
                            @endphp
                            @if ($status === 'confirmed')
                                <span class="badge bg-light-success border border-success text-success">Confirmé</span>
                            @elseif ($status === 'declined')
                                <span class="badge bg-light-danger border border-danger text-danger">Décliné</span>
                            @else
                                <span class="badge bg-light-secondary border border-secondary text-secondary">En attente</span>
                            @endif
                        </td>
                        <td>{{ $guest->created_at?->format('d/m/Y') }}</td>
                        <td>{{ $guest->deleted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="text-end">
                            <form action="{{ route('guests.restore', $guest->id) }}" method="POST" class="d-inline me-1">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm" title="Restaurer">
                                    <i class="ti ti-arrow-back-up"></i>
                                </button>
                            </form>
                            <form action="{{ route('guests.force-delete', $guest->id) }}" method="POST" class="d-inline" data-confirm="Supprimer définitivement cet invité ?" data-confirm-options='{"title": "Suppression définitive", "message": "Êtes-vous sûr de vouloir supprimer définitivement cet invité ? Cette action est irréversible et toutes les données associées seront perdues.", "confirmText": "Supprimer définitivement", "confirmClass": "btn-danger", "icon": "ti-alert-triangle", "iconColor": "text-danger"}'>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer définitivement">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

