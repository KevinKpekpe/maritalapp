<!DOCTYPE html>
<html lang="fr">
<!-- [Head] start -->

<head>
  <title>Connexion | Application Mariage</title>
  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Espace de connexion pour l'application de gestion de mariage.">
  <meta name="keywords" content="mariage, dashboard, invités, tables">
  <meta name="author" content="SpectreCoding">

  <!-- [Favicon] icon -->
  <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
  <!-- [Google Font] Family -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
  <!-- [Tabler Icons] https://tablericons.com -->
  <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
  <!-- [Feather Icons] https://feathericons.com -->
  <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
  <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
  <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
  <!-- [Material Icons] https://fonts.google.com/icons -->
  <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
  <!-- [Template CSS Files] -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
  <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->

  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="{{ url('/') }}"><img src="{{ asset('logo.png') }}" alt="logo" style="max-height: 150px; width: auto;"></a>
        </div>
        <div class="card my-5">
          <div class="card-body">
            <form method="POST" action="{{ url('/login') }}">
              @csrf
              <div class="d-flex justify-content-between align-items-end mb-4">
                <h3 class="mb-0"><b>Connexion</b></h3>
                <span class="text-muted">Accès réservé à l'équipe mariage</span>
              </div>

              @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                  {{ $errors->first() }}
                </div>
              @endif

              <div class="form-group mb-3">
                <label class="form-label" for="email">Adresse email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="admin@appmariage.com" required autofocus>
              </div>
              <div class="form-group mb-3">
                <label class="form-label" for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
              </div>
              <div class="d-flex mt-1 justify-content-between">
                <div class="form-check">
                  <input class="form-check-input input-primary" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                  <label class="form-check-label text-muted" for="remember">Se souvenir de moi</label>
                </div>
                <a href="{{ route('password.forgot') }}" class="text-secondary f-w-400">Mot de passe oublié ?</a>
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Se connecter</button>
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
  <!-- [ Main Content ] end -->
  <!-- Required Js -->
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
<!-- [Body] end -->

</html>
