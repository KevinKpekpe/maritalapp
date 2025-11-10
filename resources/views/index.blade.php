@extends('app')
@section('content')
<!-- [ Main Content ] start -->
<div class="row g-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-wrap gap-3">
                <a href="{{ url('/guests') }}" class="btn btn-light-primary">
                    <i class="ti ti-users me-2"></i> Invités
                </a>
                <a href="{{ url('/tables') }}" class="btn btn-light-warning">
                    <i class="ti ti-table me-2"></i> Tables
                </a>
                <a href="{{ url('/invitations') }}" class="btn btn-light-success">
                    <i class="ti ti-mail me-2"></i> Invitations
                </a>
                <a href="{{ url('/tasks') }}" class="btn btn-light-secondary">
                    <i class="ti ti-checklist me-2"></i> Tâches
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total des invités</h6>
                <h4 class="mb-3">180 <span class="badge bg-light-success border border-success"><i
                            class="ti ti-trending-up"></i> +12</span></h4>
                <p class="mb-0 text-muted text-sm">Ajouts cette semaine : <span class="text-success">8</span></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Confirmations reçues</h6>
                <h4 class="mb-3">124 <span class="badge bg-light-primary border border-primary"><i
                            class="ti ti-trending-up"></i> 68%</span></h4>
                <p class="mb-0 text-muted text-sm">En attente : <span class="text-primary">56</span></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Tables planifiées</h6>
                <h4 class="mb-3">18 <span class="badge bg-light-warning border border-warning"><i
                            class="ti ti-trending-up"></i> 90%</span></h4>
                <p class="mb-0 text-muted text-sm">Places restantes : <span class="text-warning">12</span></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Invitations envoyées</h6>
                <h4 class="mb-3">200 <span class="badge bg-light-danger border border-danger"><i
                            class="ti ti-trending-down"></i> 5%</span></h4>
                <p class="mb-0 text-muted text-sm">Relances prévues : <span class="text-danger">15</span></p>
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

@endsection
