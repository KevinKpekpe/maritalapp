<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Mot de passe oublié | Application Mariage</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
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
          <a href="{{ url('/') }}"><img src="{{ asset('assets/images/logo-dark.svg') }}" alt="logo"></a>
        </div>
        <div class="card my-5">
          <div class="card-body">
            <form method="POST" action="{{ route('password.send-code') }}">
              @csrf
              <div class="d-flex justify-content-between align-items-end mb-4">
                <h3 class="mb-0"><b>Mot de passe oublié</b></h3>
                <a href="{{ route('login') }}" class="link-primary">Retour à la connexion</a>
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

              <div class="form-group mb-3">
                <label class="form-label" for="email">Adresse email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="votre@email.com" required autofocus>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <p class="mt-4 text-sm text-muted">N'oubliez pas de vérifier votre boîte de réception et les spams.</p>
              <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary">Envoyer le code de réinitialisation</button>
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
