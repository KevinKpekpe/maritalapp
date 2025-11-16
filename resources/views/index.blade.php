@extends('app')
@section('content')
<!-- [ Main Content ] start -->
<div class="row g-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-wrap gap-3">
                <a href="{{ url('/guests') }}" class="btn btn-light-primary" title="Invités">
                    <i class="ti ti-users"></i>
                </a>
                <a href="{{ url('/tables') }}" class="btn btn-light-warning" title="Tables">
                    <i class="ti ti-table"></i>
                </a>
            </div>
        </div>
    </div>
    @php
        $stats = array_merge([
            'guests_total' => 0,
            'guests_pending' => 0,
            'guests_confirmed' => 0,
            'tables_total' => 0,
            'invitations_sent' => 0,
        ], $stats ?? []);

        $chartData = $chartData ?? [
            'weekly' => ['data' => [], 'labels' => []],
            'monthly' => ['data' => [], 'labels' => []],
        ];
    @endphp
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total des invités</h6>
                <h4 class="mb-0">{{ number_format($stats['guests_total']) }}</h4>
                <p class="mb-0 text-muted text-sm">Invités actifs enregistrés</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Invités en attente</h6>
                <h4 class="mb-0 text-warning">{{ number_format($stats['guests_pending']) }}</h4>
                <p class="mb-0 text-muted text-sm">RSVP non confirmé</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Confirmations reçues</h6>
                <h4 class="mb-0 text-success">{{ number_format($stats['guests_confirmed']) }}</h4>
                <p class="mb-0 text-muted text-sm">Invités ayant confirmé leur présence</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Tables actives</h6>
                <h4 class="mb-0 text-primary">{{ number_format($stats['tables_total']) }}</h4>
                <p class="mb-0 text-muted text-sm">Tables disponibles pour les invités</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Invitations envoyées</h6>
                <h4 class="mb-0 text-info">{{ number_format($stats['invitations_sent']) }}</h4>
                <p class="mb-0 text-muted text-sm">Invitations WhatsApp envoyées</p>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-8">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">Suivi des réponses</h5>
            <ul class="nav nav-pills justify-content-end mb-0" id="chart-tab-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="chart-tab-home-tab" data-bs-toggle="pill"
                        data-bs-target="#chart-tab-home" type="button" role="tab" aria-controls="chart-tab-home"
                        aria-selected="true">Mois</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="chart-tab-profile-tab" data-bs-toggle="pill"
                        data-bs-target="#chart-tab-profile" type="button" role="tab"
                        aria-controls="chart-tab-profile" aria-selected="false">Semaine</button>
                </li>
            </ul>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="tab-content" id="chart-tab-tabContent">
                    <div class="tab-pane" id="chart-tab-home" role="tabpanel" aria-labelledby="chart-tab-home-tab"
                        tabindex="0">
                        <div id="visitor-chart-1"></div>
                    </div>
                    <div class="tab-pane show active" id="chart-tab-profile" role="tabpanel"
                        aria-labelledby="chart-tab-profile-tab" tabindex="0">
                        <div id="visitor-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    // Passer les données du graphique au JavaScript
    window.chartData = {
        weekly: {
            data: @json($chartData['weekly']['data'] ?? []),
            labels: @json($chartData['weekly']['labels'] ?? [])
        },
        monthly: {
            data: @json($chartData['monthly']['data'] ?? []),
            labels: @json($chartData['monthly']['labels'] ?? [])
        }
    };
</script>
@endpush

@endsection
