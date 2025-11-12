@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $user->exists ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' }}</h5>
                <small class="text-muted">Définissez les informations de l'utilisateur pour l'accès à l'application.</small>
            </div>
            <div class="card-body">
                <form action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" method="POST">
                    @csrf
                    @if ($user->exists)
                        @method('PUT')
                    @endif

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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="name">Nom complet</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Ex : Jean Dupont" value="{{ old('name', $user->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Ex : jean.dupont@example.com" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="password">
                                    Mot de passe
                                    @if ($user->exists)
                                        <small class="text-muted">(laisser vide pour ne pas modifier)</small>
                                    @endif
                                </label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Minimum 8 caractères" {{ $user->exists ? '' : 'required' }}>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                    placeholder="Répétez le mot de passe" {{ $user->exists ? '' : 'required' }}>
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-light-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">{{ $user->exists ? 'Mettre à jour' : 'Enregistrer' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

