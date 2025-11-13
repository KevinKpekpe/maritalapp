@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Importer des invités</h5>
                <small class="text-muted">Importez une liste d'invités depuis un fichier CSV.</small>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <form action="{{ route('guests.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <label for="csv_file" class="form-label">Fichier CSV</label>
                                <div class="d-flex gap-2 align-items-center mb-2">
                                    <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                                    <a href="{{ route('guests.import.template') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="ti ti-download me-1"></i> Télécharger le modèle
                                    </a>
                                </div>
                                <small class="form-text text-muted">Format CSV avec point-virgule (;) comme séparateur. Taille max : 10MB</small>
                            </div>

                            <div class="alert alert-info">
                                <h6 class="alert-heading">Format du fichier CSV attendu :</h6>
                                <p class="mb-2">Le fichier doit contenir les colonnes suivantes (en-têtes) :</p>
                                <ul class="mb-0">
                                    <li><strong>Type</strong> : "solo" ou "couple" (obligatoire)</li>
                                    <li><strong>Prénom principal</strong> : Prénom de l'invité (obligatoire)</li>
                                    <li><strong>Prénom partenaire</strong> : Prénom du partenaire (requis si Type = "couple")</li>
                                    <li><strong>Téléphone</strong> ou <strong>Phone</strong> : Numéro de téléphone (obligatoire)</li>
                                    <li><strong>Email</strong> : Adresse email (optionnel)</li>
                                    <li><strong>Table</strong> : Nom de la table (optionnel, doit exister)</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <h6 class="alert-heading">Notes importantes :</h6>
                                <ul class="mb-0">
                                    <li>Les numéros de téléphone seront automatiquement formatés</li>
                                    <li>Les tables doivent exister avant l'import</li>
                                    <li>Les lignes avec des erreurs seront ignorées mais listées après l'import</li>
                                    <li>Le fichier doit être encodé en UTF-8</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('guests.index') }}" class="btn btn-light-secondary">Annuler</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-upload me-2"></i> Importer
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">Exemple de fichier CSV</h6>
                            </div>
                            <div class="card-body">
                                <pre class="mb-0 small">Type;Prénom principal;Prénom partenaire;Téléphone;Email;Table
solo;Jean;+243 999 123 456;jean@example.com;Table 1
couple;Marie;Pierre;+33 1 23 45 67 89;marie@example.com;Table 2
solo;Sophie;;+243 888 765 432;;Table 1</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

