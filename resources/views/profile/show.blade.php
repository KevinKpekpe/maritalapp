@extends('app')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body position-relative">
                        <div class="text-center">
                            <div class="chat-avtar d-inline-flex mx-auto">
                                <img class="rounded-circle img-fluid wid-120" src="{{ asset('assets/images/user/avatar-5.jpg') }}"
                                    alt="User image">
                            </div>
                            <h5 class="mt-3">{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->email }}</p>
                            <ul class="list-inline ms-auto mb-0">
                                <li class="list-inline-item">
                                    <a href="#" class="avtar avtar-xs btn-light-facebook">
                                        <i class="ti ti-brand-facebook f-18"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="avtar avtar-xs btn-light-twitter">
                                        <i class="ti ti-brand-twitter f-18"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="avtar avtar-xs btn-light-linkedin">
                                        <i class="ti ti-brand-linkedin f-18"></i>
                                    </a>
                                </li>
                            </ul>
                            <div class="row g-3 my-4">
                                <div class="col-4">
                                    <h5 class="mb-0">{{ \App\Models\Guest::count() }}</h5>
                                    <small class="text-muted">Invités</small>
                                </div>
                                <div class="col-4 border border-top-0 border-bottom-0">
                                    <h5 class="mb-0">{{ \App\Models\ReceptionTable::count() }}</h5>
                                    <small class="text-muted">Tables</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="mb-0">{{ \App\Models\Guest::where('rsvp_status', 'confirmed')->count() }}</h5>
                                    <small class="text-muted">Confirmés</small>
                                </div>
                            </div>
                        </div>
                        <div class="nav flex-column nav-pills list-group list-group-flush user-sett-tabs" id="user-set-tab"
                            role="tablist" aria-orientation="vertical">
                            <a class="nav-link list-group-item list-group-item-action active" id="user-tab-1"
                                data-bs-toggle="pill" href="#user-cont-1" role="tab">
                                <span class="f-w-500"><i class="ti ti-user m-r-10"></i>Informations personnelles</span>
                            </a>
                            <a class="nav-link list-group-item list-group-item-action" id="user-tab-3" data-bs-toggle="pill"
                                href="#user-cont-3" role="tab">
                                <span class="f-w-500"><i class="ti ti-lock m-r-10"></i>Changer le mot de passe</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="tab-content" id="user-set-tabContent">
                    <div class="tab-pane fade show active" id="user-cont-1" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('profile.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    @if ($errors->any())
                                        <div class="alert alert-danger" role="alert">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-12">
                                            <h5>Informations personnelles</h5>
                                            <hr class="mb-4">
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">Nom complet</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    name="name" value="{{ old('name', $user->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">Adresse email</label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                    name="email" value="{{ old('email', $user->email) }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-end btn-page mt-4">
                                        <button type="button" class="btn btn-outline-secondary" onclick="this.closest('form').reset()">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="user-cont-3" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('profile.change-password') }}" method="POST">
                                    @csrf

                                    @if ($errors->any())
                                        <div class="alert alert-danger" role="alert">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-12">
                                            <h5>Changer le mot de passe</h5>
                                            <hr class="mb-4">
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Mot de passe actuel</label>
                                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                                    name="current_password" required>
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Nouveau mot de passe</label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                    name="password" required>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Confirmer le mot de passe</label>
                                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                                    name="password_confirmation" required>
                                                @error('password_confirmation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h5>Le nouveau mot de passe doit contenir :</h5>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item"><i class="ti ti-minus me-2"></i> Au moins 8 caractères</li>
                                                <li class="list-group-item"><i class="ti ti-minus me-2"></i> Au moins 1 lettre minuscule (a-z)</li>
                                                <li class="list-group-item"><i class="ti ti-minus me-2"></i> Au moins 1 lettre majuscule (A-Z)</li>
                                                <li class="list-group-item"><i class="ti ti-minus me-2"></i> Au moins 1 chiffre (0-9)</li>
                                                <li class="list-group-item"><i class="ti ti-minus me-2"></i> Au moins 1 caractère spécial</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-footer text-end btn-page mt-4">
                                        <button type="button" class="btn btn-outline-secondary" onclick="this.closest('form').reset()">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

