@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $guest->exists ? 'Modifier un invité' : 'Ajouter un invité' }}</h5>
                <small class="text-muted">Saisissez les informations de l’invité et associez-le à une table.</small>
            </div>
            <div class="card-body">
                <form action="{{ $guest->exists ? route('guests.update', $guest) : route('guests.store') }}" method="POST">
                    @csrf
                    @if ($guest->exists)
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
                        <div class="col-md-4">
                            <label class="form-label" for="type">Type d’invité</label>
                            <select class="form-select" id="type" name="type">
                                <option value="solo" @selected(old('type', $guest->type ?? 'solo') === 'solo')>Solo</option>
                                <option value="couple" @selected(old('type', $guest->type ?? 'solo') === 'couple')>Couple</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="primary_first_name">Prénom principal</label>
                            <input type="text" class="form-control" id="primary_first_name" name="primary_first_name"
                                value="{{ old('primary_first_name', $guest->primary_first_name) }}" placeholder="Ex : Daniella" required>
                        </div>
                        <div class="col-md-4" id="secondary-name-wrapper">
                            <label class="form-label" for="secondary_first_name">Prénom du partenaire</label>
                            <input type="text" class="form-control" id="secondary_first_name" name="secondary_first_name"
                                value="{{ old('secondary_first_name', $guest->secondary_first_name) }}" placeholder="Ex : Raphaël">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="reception_table_id">Table assignée</label>
                            <select class="form-select" id="reception_table_id" name="reception_table_id" required>
                                <option value="">Sélectionnez une table</option>
                                @foreach ($tables as $tableOption)
                                    <option value="{{ $tableOption->id }}" @selected(old('reception_table_id', $guest->reception_table_id) == $tableOption->id)>
                                        {{ $tableOption->name }}
                                        @if ($tableOption->trashed())
                                            (Archivée)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="phone">Téléphone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ old('phone', $guest->phone) }}" placeholder="Ex : +243 00 00 00 00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="email">Email (optionnel)</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $guest->email) }}" placeholder="Ex : invite@example.com">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('guests.index') }}" class="btn btn-light-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">{{ $guest->exists ? 'Mettre à jour' : 'Enregistrer' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const typeSelect = document.getElementById('type');
        const secondaryWrapper = document.getElementById('secondary-name-wrapper');
        const secondaryInput = document.getElementById('secondary_first_name');

        function toggleSecondaryField() {
            const isCouple = typeSelect.value === 'couple';
            secondaryWrapper.style.display = isCouple ? 'block' : 'none';
            secondaryInput.required = isCouple;
            if (!isCouple) {
                secondaryInput.value = '';
            }
        }

        typeSelect.addEventListener('change', toggleSecondaryField);
        toggleSecondaryField();
    })();
</script>
@endpush
