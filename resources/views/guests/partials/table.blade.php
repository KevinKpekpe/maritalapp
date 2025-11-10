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
                    <th>Invité(s)</th>
                    <th>Type</th>
                    <th>Table</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Créé le</th>
                    <th>Archivé le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guests as $guest)
                    <tr @class(['table-danger' => $guest->trashed()])>
                        <td>
                            <span class="fw-semibold">{{ $guest->display_name }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light-primary border border-primary text-primary text-capitalize">{{ $guest->type }}</span>
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
                        <td>{{ $guest->email ?? '—' }}</td>
                        <td>{{ $guest->created_at?->format('d/m/Y') }}</td>
                        <td>{{ $guest->deleted_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-end">
                            @if ($guest->trashed())
                                <form action="{{ route('guests.restore', $guest->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Restaurer">
                                        <i class="ti ti-restore me-1"></i> Restaurer
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('guests.edit', $guest) }}" class="btn btn-outline-warning btn-sm me-2" title="Modifier">
                                    <i class="ti ti-edit me-1"></i> Modifier
                                </a>
                                <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="d-inline" onsubmit="return confirm('Archiver cet invité ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Archiver">
                                        <i class="ti ti-archive me-1"></i> Archiver
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
