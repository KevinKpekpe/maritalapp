@if ($summary->isEmpty())
    <div class="alert alert-secondary" role="alert">
        Aucune préférence enregistrée pour le moment. Commencez par ajouter une boisson pour un invité.
    </div>
@else
    <div class="row g-3">
        @foreach ($summary as $item)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $item['beverage']->name ?? 'Boisson inconnue' }}</h6>
                                <span class="badge bg-light-primary border border-primary text-capitalize">
                                    {{ $item['beverage']->category ?? 'n/a' }}
                                </span>
                            </div>
                            <span class="fs-4 fw-semibold text-primary">{{ $item['count'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
