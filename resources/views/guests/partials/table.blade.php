@if ($guests->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="ti ti-users me-2"></i> Aucun invité trouvé.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="guests-dt">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="select-all-guests" title="Sélectionner tout">
                    </th>
                    <th>Invité(s)</th>
                    <th class="d-none d-md-table-cell">Type</th>
                    <th class="d-none d-lg-table-cell">Table</th>
                    <th class="d-none d-xl-table-cell">Téléphone</th>
                    {{-- <th>Email</th> --}}
                    <th class="d-none d-md-table-cell">Statut RSVP</th>
                    <th class="d-none d-lg-table-cell">Invitation</th>
                    <th class="d-none d-md-table-cell">WhatsApp</th>
                    <th class="d-none d-xl-table-cell">Créé le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guests as $guest)
                    <tr>
                        <td>
                            @if (!$guest->whatsapp_sent_at)
                                <input type="checkbox" class="guest-checkbox" name="selected_guests[]" value="{{ $guest->id }}" data-guest-id="{{ $guest->id }}">
                            @else
                                <span class="text-muted" title="Invitation déjà envoyée">
                                    <i class="ti ti-check text-success"></i>
                                </span>
                            @endif
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
                                    @if ($guest->table)
                                        <span class="badge bg-light-info border border-info text-info small">
                                            <i class="ti ti-table"></i> {{ $guest->table->name }}
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
                        <td class="d-none d-lg-table-cell">
                            @if ($guest->table)
                                {{ $guest->table->name }}
                                @if ($guest->table->trashed())
                                    <span class="badge bg-light-secondary border border-secondary ms-1">Table archivée</span>
                                @endif
                            @else
                                <span class="text-muted">Non définie</span>
                            @endif
                        </td>
                        <td class="d-none d-xl-table-cell">{{ $guest->phone }}</td>
                        {{-- <td>{{ $guest->email ?? '—' }}</td> --}}
                        <td class="d-none d-md-table-cell">
                            @php
                                $status = $guest->rsvp_status;
                            @endphp
                            @if ($status === 'confirmed')
                                <span class="badge bg-light-success border border-success text-success">
                                    Confirmé
                                    @if ($guest->rsvp_confirmed_at)
                                        <span class="d-block small fw-normal text-success mt-1">
                                            {{ $guest->rsvp_confirmed_at->format('d/m/Y H\hi') }}
                                        </span>
                                    @endif
                                </span>
                            @elseif ($status === 'declined')
                                <span class="badge bg-light-danger border border-danger text-danger">Décliné</span>
                            @else
                                <span class="badge bg-light-secondary border border-secondary text-secondary">En attente</span>
                            @endif
                        </td>
                        <td class="d-none d-lg-table-cell">
                            @if ($guest->invitation_token)
                                <a href="{{ route('invitations.show', $guest->invitation_token) }}" class="btn btn-soft-primary btn-sm" target="_blank" rel="noopener">
                                    <i class="ti ti-external-link me-1"></i> Voir
                                </a>
                            @else
                                <span class="text-muted">Non générée</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if ($guest->whatsapp_sent_at)
                                <span class="badge bg-light-success border border-success text-success">
                                    <i class="ti ti-check me-1"></i> Envoyé
                                    <span class="d-block small fw-normal text-success mt-1">
                                        {{ $guest->whatsapp_sent_at->format('d/m/Y H\hi') }}
                                    </span>
                                </span>
                            @else
                                <span class="badge bg-light-secondary border border-secondary text-secondary">
                                    <i class="ti ti-x me-1"></i> Non envoyé
                                </span>
                            @endif
                        </td>
                        <td class="d-none d-xl-table-cell">{{ $guest->created_at?->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <div class="d-flex flex-column flex-md-row gap-1 gap-md-2 align-items-end align-items-md-center justify-content-end">
                                <div class="btn-group" role="group">
                                    <form action="{{ route('guests.send_invitation', $guest) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm" title="Envoyer le lien d'invitation WhatsApp">
                                            <i class="ti ti-brand-whatsapp me-1"></i>
                                            <i class="ti ti-link"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('guests.send_invitation_pdf', $guest) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm" title="Envoyer le PDF de l'invitation WhatsApp">
                                            <i class="ti ti-brand-whatsapp me-1"></i>
                                            <i class="ti ti-file-pdf"></i>
                                        </button>
                                    </form>
                                </div>
                                <a href="{{ route('guests.edit', $guest) }}" class="btn btn-outline-warning btn-sm" title="Modifier">
                                    <i class="ti ti-edit"></i>
                                </a>
                            <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="d-inline" data-confirm="Archiver cet invité ?" data-confirm-options='{"title": "Archiver l&#39;invité", "message": "Êtes-vous sûr de vouloir archiver cet invité ? Il sera déplacé dans la corbeille.", "confirmText": "Archiver", "confirmClass": "btn-warning", "icon": "ti-archive", "iconColor": "text-warning"}'>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Archiver">
                                    <i class="ti ti-archive"></i>
                                </button>
                            </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
