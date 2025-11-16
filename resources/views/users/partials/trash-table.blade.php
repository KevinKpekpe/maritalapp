@if ($users->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="ti ti-trash me-2"></i> Aucun utilisateur archivé.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Créé le</th>
                    <th>Archivé le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-primary text-white">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    {{ $user->name }}
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td>{{ $user->deleted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="text-end">
                            <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline me-1">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm" title="Restaurer">
                                    <i class="ti ti-arrow-back-up"></i>
                                </button>
                            </form>
                            <form action="{{ route('users.force-delete', $user->id) }}" method="POST" class="d-inline" data-confirm="Supprimer définitivement cet utilisateur ?" data-confirm-options='{"title": "Suppression définitive", "message": "Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ? Cette action est irréversible et toutes les données associées seront perdues.", "confirmText": "Supprimer définitivement", "confirmClass": "btn-danger", "icon": "ti-alert-triangle", "iconColor": "text-danger"}'>
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

