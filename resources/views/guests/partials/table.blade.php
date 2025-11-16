@if ($guests->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="ti ti-users me-2"></i> Aucun invité trouvé.
    </div>
@else
    <div class="alert alert-info d-flex align-items-center" role="alert">
        <i class="ti ti-info-circle me-2"></i>
        <span>Les invités archivés apparaissent en rouge. Utilisez "Restaurer" pour les réactiver.</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="guests-dt">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="select-all-guests" title="Sélectionner tout">
                    </th>
                    <th>Invité(s)</th>
                    <th>Type</th>
                    <th>Table</th>
                    <th>Téléphone</th>
                    {{-- <th>Email</th> --}}
                    <th>Statut RSVP</th>
                    <th>Invitation</th>
                    <th>WhatsApp</th>
                    <th>Créé le</th>
                    <th>Archivé le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guests as $guest)
                    <tr @class(['table-danger' => $guest->trashed()])>
                        <td>
                            @if (!$guest->trashed() && !$guest->whatsapp_sent_at)
                                <input type="checkbox" class="guest-checkbox" name="selected_guests[]" value="{{ $guest->id }}" data-guest-id="{{ $guest->id }}">
                            @elseif (!$guest->trashed() && $guest->whatsapp_sent_at)
                                <span class="text-muted" title="Invitation déjà envoyée">
                                    <i class="ti ti-check text-success"></i>
                                </span>
                            @endif
                        </td>
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
                                @if ($guest->table->trashed())
                                    <span class="badge bg-light-secondary border border-secondary ms-1">Table archivée</span>
                                @endif
                            @else
                                <span class="text-muted">Non définie</span>
                            @endif
                        </td>
                        <td>{{ $guest->phone }}</td>
                        {{-- <td>{{ $guest->email ?? '—' }}</td> --}}
                        <td>
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
                        <td>
                            @if ($guest->invitation_token)
                                <a href="{{ route('invitations.show', $guest->invitation_token) }}" class="btn btn-soft-primary btn-sm" target="_blank" rel="noopener">
                                    <i class="ti ti-external-link me-1"></i> Voir
                                </a>
                            @else
                                <span class="text-muted">Non générée</span>
                            @endif
                        </td>
                        <td>
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
                        <td>{{ $guest->created_at?->format('d/m/Y') }}</td>
                        <td>{{ $guest->deleted_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-end">
                            @if ($guest->trashed())
                                <form action="{{ route('guests.restore', $guest->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Restaurer">
                                        <i class="ti ti-restore me-1"></i>
                                        {{-- Restaurer --}}
                                    </button>
                                </form>
                            @else
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
                                <a href="{{ route('guests.edit', $guest) }}" class="btn btn-outline-warning btn-sm me-2" title="Modifier">
                                    <i class="ti ti-edit me-1"></i>
                                    {{-- Modifier --}}
                                </a>
                                <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="d-inline" onsubmit="return confirm('Archiver cet invité ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Archiver">
                                        <i class="ti ti-archive me-1"></i>
                                        {{-- Archiver --}}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
