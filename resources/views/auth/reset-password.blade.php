<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Réinitialiser le mot de passe | Application Mariage</title>
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
            <form method="POST" action="{{ route('password.update') }}">
              @csrf
              <div class="mb-4">
                <h3 class="mb-2"><b>Réinitialiser le mot de passe</b></h3>
                <p class="text-muted">Veuillez choisir votre nouveau mot de passe</p>
                <p class="text-muted small">Email : <strong>{{ $email }}</strong></p>
              </div>

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

              <div class="form-group mb-3">
                <label class="form-label" for="password">Nouveau mot de passe</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Mot de passe" required>
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Minimum 8 caractères</small>
              </div>
              <div class="form-group mb-3">
                <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Réinitialiser le mot de passe</button>
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
  <script>layout_change('light');</script>
  <script>change_box_container('false');</script>
  <script>layout_rtl_change('false');</script>
  <script>preset_change("preset-1");</script>
  <script>font_change("Public-Sans");</script>
</body>
</html>
