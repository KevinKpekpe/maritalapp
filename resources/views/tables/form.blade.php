@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $table->exists ? 'Modifier la table' : 'Ajouter une table' }}</h5>
                <small class="text-muted">Définissez les informations de la table pour l’organisation du mariage.</small>
            </div>
            <div class="card-body">
                <form action="{{ $table->exists ? route('tables.update', $table) : route('tables.store') }}" method="POST">
                    @csrf
                    @if ($table->exists)
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
                                <label class="form-label" for="name">Nom de la table</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Ex : Table VIP" value="{{ old('name', $table->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Invités prévus, emplacement, ambiance, etc.">{{ old('description', $table->description) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="is_active">Active</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" @selected(old('is_active', $table->is_active ?? true) == true)>Oui</option>
                                    <option value="0" @selected(old('is_active', $table->is_active ?? true) == false)>Non</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('tables.index') }}" class="btn btn-light-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">{{ $table->exists ? 'Mettre à jour' : 'Enregistrer' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
