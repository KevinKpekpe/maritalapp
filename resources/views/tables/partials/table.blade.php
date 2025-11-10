@if ($tables->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="ti ti-layout-grid me-2"></i> Aucune table trouvée.
    </div>
@else
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="ti ti-info-circle me-2"></i>
        <span>Les tables archivées apparaissent en rouge. Utilisez "Restaurer" pour les réactiver.</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tables-dt">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Active</th>
                    <th>Statut</th>
                    <th>Créée le</th>
                    <th>Archivée le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tables as $table)
                    <tr @class(['table-danger' => $table->trashed()])>
                        <td>{{ $table->name }}</td>
                        <td class="text-muted">{{ $table->description ? \Illuminate\Support\Str::limit($table->description, 70) : '—' }}</td>
                        <td>
                            @if ($table->is_active)
                                <span class="badge bg-light-success border border-success text-success">Oui</span>
                            @else
                                <span class="badge bg-light-danger border border-danger text-danger">Non</span>
                            @endif
                        </td>
                        <td>
                            @if ($table->trashed())
                                <span class="badge bg-light-secondary border border-secondary text-secondary">Archivée</span>
                            @else
                                <span class="badge bg-light-primary border border-primary text-primary">Active</span>
                            @endif
                        </td>
                        <td>{{ $table->created_at?->format('d/m/Y') }}</td>
                        <td>{{ $table->deleted_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-end">
                            @if ($table->trashed())
                                <form action="{{ route('tables.restore', $table->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Restaurer">
                                        <i class="ti ti-restore me-1"></i> Restaurer
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('tables.edit', $table) }}" class="btn btn-outline-warning btn-sm me-2" title="Modifier">
                                    <i class="ti ti-edit me-1"></i> Modifier
                                </a>
                                <form action="{{ route('tables.destroy', $table) }}" method="POST" class="d-inline" onsubmit="return confirm('Archiver cette table ?');">
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
