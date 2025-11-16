@if ($tables->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="ti ti-trash me-2"></i> Aucune table archivée.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Active</th>
                    <th>Créée le</th>
                    <th>Archivée le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tables as $table)
                    <tr>
                        <td>{{ $table->name }}</td>
                        <td class="text-muted">{{ $table->description ? \Illuminate\Support\Str::limit($table->description, 70) : '—' }}</td>
                        <td>
                            @if ($table->is_active)
                                <span class="badge bg-light-success border border-success text-success">Oui</span>
                            @else
                                <span class="badge bg-light-danger border border-danger text-danger">Non</span>
                            @endif
                        </td>
                        <td>{{ $table->created_at?->format('d/m/Y') }}</td>
                        <td>{{ $table->deleted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="text-end">
                            <form action="{{ route('tables.restore', $table->id) }}" method="POST" class="d-inline me-1">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm" title="Restaurer">
                                    <i class="ti ti-arrow-back-up"></i>
                                </button>
                            </form>
                            <form action="{{ route('tables.force-delete', $table->id) }}" method="POST" class="d-inline" data-confirm="Supprimer définitivement cette table ?" data-confirm-options='{"title": "Suppression définitive", "message": "Êtes-vous sûr de vouloir supprimer définitivement cette table ? Cette action est irréversible. Vous ne pourrez pas supprimer une table qui contient encore des invités.", "confirmText": "Supprimer définitivement", "confirmClass": "btn-danger", "icon": "ti-alert-triangle", "iconColor": "text-danger"}'>
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

