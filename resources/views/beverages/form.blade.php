@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $beverage->exists ? 'Modifier une boisson' : 'Ajouter une boisson' }}</h5>
                <small class="text-muted">Définissez la liste des boissons proposées aux invités.</small>
            </div>
            <div class="card-body">
                <form action="{{ $beverage->exists ? route('beverages.update', $beverage) : route('beverages.store') }}" method="POST">
                    @csrf
                    @if ($beverage->exists)
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

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Nom de la boisson</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $beverage->name) }}" placeholder="Ex : Champagne Brut" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="category">Catégorie</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Sélectionner</option>
                                <option value="alcool" @selected(old('category', $beverage->category) === 'alcool')>Alcool</option>
                                <option value="sucre" @selected(old('category', $beverage->category) === 'sucre')>Sucré</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="is_active">Statut</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1" @selected(old('is_active', $beverage->is_active ?? true))>Active</option>
                                <option value="0" @selected(old('is_active', $beverage->is_active ?? true) == false)>Inactif</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('beverages.index') }}" class="btn btn-light-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">{{ $beverage->exists ? 'Mettre à jour' : 'Enregistrer' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
