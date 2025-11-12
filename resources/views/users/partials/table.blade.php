@if ($users->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="ti ti-users me-2"></i> Aucun utilisateur trouvé.
    </div>
@else
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="ti ti-info-circle me-2"></i>
        <span>Les utilisateurs archivés apparaissent en rouge. Utilisez "Restaurer" pour les réactiver.</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="users-dt">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Créé le</th>
                    <th>Archivé le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr @class(['table-danger' => $user->trashed()])>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-primary text-white">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    {{ $user->name }}
                                    @if ($user->id === auth()->id())
                                        <span class="badge bg-light-info border border-info text-info ms-2">Vous</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->trashed())
                                <span class="badge bg-light-secondary border border-secondary text-secondary">Archivé</span>
                            @else
                                <span class="badge bg-light-primary border border-primary text-primary">Actif</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td>{{ $user->deleted_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-end">
                            @if ($user->trashed())
                                <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Restaurer">
                                        <i class="ti ti-restore me-1"></i> Restaurer
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning btn-sm me-2" title="Modifier">
                                    <i class="ti ti-edit me-1"></i> Modifier
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Archiver cet utilisateur ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Archiver"
                                        @if ($user->id === auth()->id()) disabled title="Vous ne pouvez pas supprimer votre propre compte" @endif>
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

