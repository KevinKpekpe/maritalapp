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
                            <div class="invalid-feedback" id="phone-error"></div>
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

        // Validation du numéro de téléphone
        const phoneInput = document.getElementById('phone');
        const phoneError = document.getElementById('phone-error');
        const form = phoneInput.closest('form');

        function validatePhone(phone) {
            // Extraire uniquement les chiffres
            const digits = phone.replace(/\D+/g, '');

            // Vérifier qu'il y a au moins des chiffres
            if (digits.length === 0) {
                return { valid: false, message: 'Le numéro de téléphone est requis.' };
            }

            // Vérifier la longueur minimale (au moins 8 chiffres pour un numéro local)
            if (digits.length < 8) {
                return { valid: false, message: 'Le numéro de téléphone est trop court.' };
            }

            // Vérifier la longueur maximale (15 chiffres max pour un numéro international)
            if (digits.length > 15) {
                return { valid: false, message: 'Le numéro de téléphone est trop long.' };
            }

            // Vérifier les préfixes internationaux valides (1 à 3 chiffres)
            const internationalPrefixes = {
                1: ['1'],
                2: ['20', '27', '30', '31', '32', '33', '34', '36', '39', '40', '41', '43', '44', '45', '46', '47', '48', '49', '51', '52', '53', '54', '55', '56', '57', '58', '60', '61', '62', '63', '64', '65', '66', '81', '82', '84', '86', '90', '91', '92', '93', '94', '95', '98'],
                3: ['212', '213', '216', '218', '220', '221', '222', '223', '224', '225', '226', '227', '228', '229', '230', '231', '232', '233', '234', '235', '236', '237', '238', '239', '240', '241', '242', '243', '244', '245', '246', '247', '248', '249', '250', '251', '252', '253', '254', '255', '256', '257', '258', '260', '261', '262', '263', '264', '265', '266', '267', '268', '269', '290', '291', '297', '298', '299', '350', '351', '352', '353', '354', '355', '356', '357', '358', '359', '370', '371', '372', '373', '374', '375', '376', '377', '378', '380', '381', '382', '383', '385', '386', '387', '389', '420', '421', '423', '500', '501', '502', '503', '504', '505', '506', '507', '508', '509', '590', '591', '592', '593', '594', '595', '596', '597', '598', '599', '670', '672', '673', '674', '675', '676', '677', '678', '679', '680', '681', '682', '683', '684', '685', '686', '687', '688', '689', '690', '691', '692', '850', '852', '853', '855', '856', '880', '886', '960', '961', '962', '963', '964', '965', '966', '967', '968', '970', '971', '972', '973', '974', '975', '976', '977', '992', '993', '994', '995', '996', '998']
            };

            // Si le numéro a 10 chiffres ou moins, c'est probablement un numéro local (OK)
            if (digits.length <= 10) {
                return { valid: true };
            }

            // Vérifier les préfixes internationaux
            if (digits.length >= 11) {
                // Vérifier préfixe 1 chiffre
                if (internationalPrefixes[1].includes(digits.substring(0, 1))) {
                    if (digits.length >= 11) {
                        return { valid: true };
                    }
                }

                // Vérifier préfixe 2 chiffres
                if (digits.length >= 11) {
                    const twoDigitPrefix = digits.substring(0, 2);
                    if (internationalPrefixes[2].includes(twoDigitPrefix)) {
                        return { valid: true };
                    }
                }

                // Vérifier préfixe 3 chiffres
                if (digits.length >= 12) {
                    const threeDigitPrefix = digits.substring(0, 3);
                    if (internationalPrefixes[3].includes(threeDigitPrefix)) {
                        return { valid: true };
                    }
                }
            }

            // Si on arrive ici et que le numéro fait plus de 10 chiffres sans préfixe valide
            if (digits.length > 10) {
                return { valid: false, message: 'Le préfixe international du numéro de téléphone n\'est pas valide.' };
            }

            return { valid: true };
        }

        function showPhoneError(message) {
            phoneInput.classList.add('is-invalid');
            phoneError.textContent = message;
            phoneError.style.display = 'block';
        }

        function clearPhoneError() {
            phoneInput.classList.remove('is-invalid');
            phoneError.textContent = '';
            phoneError.style.display = 'none';
        }

        phoneInput.addEventListener('blur', function() {
            const validation = validatePhone(phoneInput.value);
            if (!validation.valid) {
                showPhoneError(validation.message);
            } else {
                clearPhoneError();
            }
        });

        phoneInput.addEventListener('input', function() {
            if (phoneInput.classList.contains('is-invalid')) {
                const validation = validatePhone(phoneInput.value);
                if (validation.valid) {
                    clearPhoneError();
                }
            }
        });

        form.addEventListener('submit', function(e) {
            const validation = validatePhone(phoneInput.value);
            if (!validation.valid) {
                e.preventDefault();
                showPhoneError(validation.message);
                phoneInput.focus();
                return false;
            }
        });
    })();
</script>
@endpush
