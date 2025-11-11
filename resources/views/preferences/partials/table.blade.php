@if ($preferences->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="ti ti-mood-empty me-2"></i> Aucune préférence trouvée.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Invité(s)</th>
                    <th>Table</th>
                    <th>Boisson</th>
                    <th>Catégorie</th>
                    <th>Notes</th>
                    <th>Ajoutée le</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($preferences as $preference)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $preference->guest?->display_name ?? 'Invité inconnu' }}</div>
                            @if ($preference->guest?->trashed())
                                <span class="badge bg-light-secondary border border-secondary">Invité archivé</span>
                            @endif
                        </td>
                        <td>
                            @if ($preference->guest?->table)
                                {{ $preference->guest->table->name }}
                                @if ($preference->guest->table->trashed())
                                    <span class="badge bg-light-secondary border border-secondary ms-1">Table archivée</span>
                                @endif
                            @else
                                <span class="text-muted">Non définie</span>
                            @endif
                        </td>
                        <td>{{ $preference->beverage?->name ?? 'Boisson inconnue' }}</td>
                        <td>
                            <span class="badge bg-light-primary border border-primary text-capitalize">
                                {{ $preference->beverage?->category ?? 'n/a' }}
                            </span>
                        </td>
                        <td>{{ $preference->notes ? \Illuminate\Support\Str::limit($preference->notes, 80) : '—' }}</td>
                        <td>{{ $preference->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
