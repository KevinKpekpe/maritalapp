<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Vérification du code | Application Mariage</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/favicon.svg') }}">
  <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
</head>
<body>
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="{{ url('/') }}"><img src="{{ asset('logo.png') }}" alt="logo" style="max-height: 150px; width: auto;"></a>
        </div>
        <div class="card my-5">
          <div class="card-body">
            <form method="POST" action="{{ route('password.verify-code') }}" id="code-form">
              @csrf
              <div class="mb-4">
                <h3 class="mb-2"><b>Entrez le code de vérification</b></h3>
                <p class="text-muted mb-4">Nous vous avons envoyé un code par email.</p>
                <p class="">Code envoyé à : <strong>{{ $email }}</strong></p>
              </div>

              @if (session('status'))
                <div class="alert alert-success" role="alert">
                  {{ session('status') }}
                </div>
              @endif

              @if (session('error'))
                <div class="alert alert-danger" role="alert">
                  {{ session('error') }}
                </div>
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

              <div class="mb-3">
                <label class="form-label">Code de vérification</label>
                <input type="text" name="code" id="code" required pattern="[0-9]{6}" class="form-control text-center" placeholder="Entrez le code à 6 chiffres" maxlength="6" inputmode="numeric" autocomplete="off" style="font-size: 24px; font-weight: bold; letter-spacing: 8px;">
                @error('code')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Continuer</button>
              </div>
              <div class="d-flex justify-content-between align-items-end mt-3">
                <p class="mb-0 text-muted">Vous n'avez pas reçu l'email ? Vérifiez vos spams, ou</p>
                <form method="POST" action="{{ route('password.resend-code') }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-link link-primary p-0">Renvoyer le code</button>
                </form>
              </div>
            </form>
          </div>
        </div>
        <div class="auth-footer row">
          <div class="col my-1">
            <p class="m-0">© {{ date('Y') }} SpectreCoding.</p>
          </div>
          <div class="col-auto my-1">
            <ul class="list-inline footer-link mb-0">
              <li class="list-inline-item"><a href="{{ url('/') }}">Accueil</a></li>
              <li class="list-inline-item"><a href="#">Politique de confidentialité</a></li>
              <li class="list-inline-item"><a href="#">Nous contacter</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
  <script src="{{ asset('assets/js/pcoded.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
  <script>
    layout_change('light');
    change_box_container('false');
    layout_rtl_change('false');
    preset_change("preset-1");
    font_change("Public-Sans");

    // Gestion de la saisie du code
    (function() {
      const codeInput = document.getElementById('code');
      const form = document.getElementById('code-form');

      if (!form || !codeInput) {
        console.error('Éléments du formulaire non trouvés');
        return;
      }

      // Filtrer pour n'accepter que les chiffres
      codeInput.addEventListener('input', function(e) {
        const value = e.target.value.replace(/[^0-9]/g, '');
        e.target.value = value;
      });

      // Gérer le collage
      codeInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
        e.target.value = pastedData;
      });

      // Validation avant soumission
      form.addEventListener('submit', function(e) {
        const code = codeInput.value.trim();

        if (!code || code.length !== 6) {
          e.preventDefault();
          e.stopPropagation();
          alert('Veuillez entrer le code complet à 6 chiffres.');
          codeInput.focus();
          return false;
        }
      });

      // Focus automatique sur le champ au chargement
      codeInput.focus();
    })();
  </script>
</body>
</html>
