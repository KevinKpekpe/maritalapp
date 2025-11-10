@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $preference->exists ? 'Modifier une préférence' : 'Ajouter une préférence' }}</h5>
                <small class="text-muted">Associez une boisson à un invité et, si besoin, ajoutez une note.</small>
            </div>
            <div class="card-body">
                <form action="{{ $preference->exists ? route('preferences.update', $preference) : route('preferences.store') }}" method="POST">
                    @csrf
                    @if ($preference->exists)
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
                            <label class="form-label" for="guest_id">Invité</label>
                            <select class="form-select" id="guest_id" name="guest_id" required>
                                <option value="">Sélectionnez un invité</option>
                                @foreach ($guestsList as $guest)
                                    <option value="{{ $guest->id }}" @selected(old('guest_id', $preference->guest_id) == $guest->id)>
                                        {{ $guest->display_name }}
                                        @if ($guest->table)
                                            – {{ $guest->table->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="beverage_id">Boisson</label>
                            <select class="form-select" id="beverage_id" name="beverage_id" required>
                                <option value="">Sélectionnez une boisson</option>
                                @foreach ($beveragesList as $beverage)
                                    <option value="{{ $beverage->id }}" @selected(old('beverage_id', $preference->beverage_id) == $beverage->id)>
                                        {{ $beverage->name }} ({{ $beverage->category }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="notes">Notes (optionnel)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Allergies, préférences particulières, etc.">{{ old('notes', $preference->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('preferences.index') }}" class="btn btn-light-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">{{ $preference->exists ? 'Mettre à jour' : 'Enregistrer' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
