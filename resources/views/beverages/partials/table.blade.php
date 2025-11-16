@if ($beverages->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="ti ti-bottle me-2"></i> Aucune boisson enregistrée.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Statut</th>
                    <th>Créée le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($beverages as $beverage)
                    <tr>
                        <td>{{ $beverage->name }}</td>
                        <td>
                            <span class="badge bg-light-primary border border-primary text-capitalize">{{ $beverage->category }}</span>
                        </td>
                        <td>
                            @if ($beverage->is_active)
                                <span class="badge bg-light-success border border-success text-success">Active</span>
                            @else
                                <span class="badge bg-light-secondary border border-secondary text-secondary">Inactif</span>
                            @endif
                        </td>
                        <td>{{ $beverage->created_at?->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('beverages.edit', $beverage) }}" class="btn btn-outline-warning btn-sm me-2" title="Modifier">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form action="{{ route('beverages.destroy', $beverage) }}" method="POST" class="d-inline" data-confirm="Supprimer cette boisson ?" data-confirm-options='{"title": "Supprimer la boisson", "message": "Êtes-vous sûr de vouloir supprimer cette boisson ? Cette action est irréversible.", "confirmText": "Supprimer", "confirmClass": "btn-danger", "icon": "ti-trash", "iconColor": "text-danger"}'>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
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
