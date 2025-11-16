<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Home | Application Mariage</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Application de gestion de mariage">
    <meta name="keywords" content="Mariage, Gestion, Invités">
    <meta name="author" content="Votre Organisation">

    <!-- [Favicon] -->
    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">

    <!-- [Google Font] -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">

    <!-- [Icons] -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

    <!-- [Styles] -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
</head>

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="{{ url('/') }}" class="b-brand text-primary" style="text-decoration: none;">
                    <img src="{{ asset('logo.png') }}" alt="Logo" style="max-height: 120px; width: auto;">
                </a>
            </div>
            <div class="navbar-content">
                <ul class="pc-navbar">
                    <!-- Tableau de bord -->
                    <li class="pc-item">
                        <a href="{{ url('/') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>
                    <!-- Gestion des invités -->
                    <li class="pc-item">
                        <a href="{{ route('guests.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-users"></i></span>
                            <span class="pc-mtext">Invités</span>
                        </a>
                    </li>
                    <!-- Plan de tables -->
                    <li class="pc-item">
                        <a href="{{ route('tables.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-table"></i></span>
                            <span class="pc-mtext">Tables</span>
                        </a>
                    </li>
                    <!-- Préférences -->
                    <li class="pc-item">
                        <a href="{{ route('preferences.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-list-check"></i></span>
                            <span class="pc-mtext">Préférences</span>
                        </a>
                    </li>
                    <!-- Boissons -->
                    <li class="pc-item">
                        <a href="{{ route('beverages.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-bottle"></i></span>
                            <span class="pc-mtext">Boissons</span>
                        </a>
                    </li>
                    <!-- Gestion des utilisateurs -->
                    <li class="pc-item">
                        <a href="{{ route('users.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-user"></i></span>
                            <span class="pc-mtext">Utilisateurs</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    <header class="pc-header" style="overflow: visible;">
        <div class="header-wrapper" style="overflow: visible;">
            <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="dropdown pc-h-item d-inline-flex d-md-none">
                        <a class="pc-head-link dropdown-toggle arrow-none m-0" data-bs-toggle="dropdown" href="#"
                            role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="ti ti-search"></i>
                        </a>
                        <div class="dropdown-menu pc-h-dropdown drp-search">
                            <form class="px-3">
                                <div class="form-group mb-0 d-flex align-items-center">
                                    <i data-feather="search"></i>
                                    <input type="search" class="form-control border-0 shadow-none"
                                        placeholder="Search here. . .">
                                </div>
                            </form>
                        </div>
                    </li>
                    {{-- <li class="pc-h-item d-none d-md-inline-flex">
                        <form class="header-search">
                            <i data-feather="search" class="icon-search"></i>
                            <input type="search" class="form-control" placeholder="Search here. . .">
                        </form>
                    </li> --}}
                </ul>
            </div>
            <!-- [Mobile Media Block end] -->
            <div class="ms-auto" style="overflow: visible;">
                <ul class="list-unstyled" style="overflow: visible;">
                    <!-- Notifications -->
                    <li class="dropdown pc-h-item" style="position: relative; overflow: visible;">
                        <a class="pc-head-link dropdown-toggle arrow-none me-2" data-bs-toggle="dropdown" href="#"
                            role="button" aria-haspopup="true" aria-expanded="false" id="notificationDropdown" style="position: relative; overflow: visible;">
                            <i class="ti ti-mail"></i>
                            <span class="badge bg-danger rounded-pill" id="notificationBadge" style="display: none; position: absolute; top: -8px; right: -6px; z-index: 1000;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown" id="notificationDropdownMenu"
                            style="max-width: 350px; width: 350px;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Notifications</h6>
                                <button class="btn btn-sm btn-link text-primary p-0" id="markAllReadBtn" style="display: none;">
                                    Tout marquer comme lu
                                </button>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div id="notificationsList" style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center p-3 text-muted">
                                    <i class="ti ti-loader-2 spin" id="notificationLoader"></i>
                                    <p class="mb-0">Chargement...</p>
                                </div>
                            </div>
                            <div class="dropdown-divider" id="notificationFooter" style="display: none;"></div>
                            <div class="text-center p-2" id="noNotifications" style="display: none;">
                                <p class="text-muted mb-0">Aucune notification</p>
                            </div>
                        </div>
                    </li>
                    <!-- Profile -->
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside"
                            aria-expanded="false">
                            <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="user-image"
                                class="user-avtar">
                            <span>{{ Auth::user()->name ?? 'Utilisateur' }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex mb-1">
                                    <div class="shrink-0">
                                        <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="user-image"
                                            class="user-avtar wid-35">
                                    </div>
                                    <div class="grow ms-3">
                                        <h6 class="mb-1">{{ Auth::user()->name ?? 'Utilisateur' }}</h6>
                                        <span class="text-muted">{{ Auth::user()->email ?? '' }}</span>
                                    </div>
                                    <a href="{{ route('logout') }}" class="pc-head-link bg-transparent"><i
                                            class="ti ti-power text-danger"></i></a>
                                </div>
                            </div>
                            <ul class="nav drp-tabs nav-fill nav-tabs" id="mydrpTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="drp-t1" data-bs-toggle="tab"
                                        data-bs-target="#drp-tab-1" type="button" role="tab"
                                        aria-controls="drp-tab-1" aria-selected="true"><i class="ti ti-user"></i>
                                        Profil</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="mysrpTabContent">
                                <div class="tab-pane fade show active" id="drp-tab-1" role="tabpanel"
                                    aria-labelledby="drp-t1" tabindex="0">
                                    <a href="{{ route('profile.show') }}" class="dropdown-item">
                                        <i class="ti ti-user"></i>
                                        <span>Mon Profil</span>
                                    </a>
                                    <a href="{{ route('logout') }}" class="dropdown-item">
                                        <i class="ti ti-power"></i>
                                        <span>Se déconnecter</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <!-- [ Header ] end -->


    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">{{ $pageTitle ?? 'Dashboard' }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                @if (isset($breadcrumbs) && is_array($breadcrumbs) && count($breadcrumbs) > 0)
                                    @foreach ($breadcrumbs as $index => $breadcrumb)
                                        @if ($index === count($breadcrumbs) - 1)
                                            <li class="breadcrumb-item" aria-current="page">
                                                {{ $breadcrumb['label'] }}</li>
                                        @else
                                            <li class="breadcrumb-item">
                                                <a
                                                    href="{{ $breadcrumb['url'] ?? '#' }}">{{ $breadcrumb['label'] }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                @else
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
                                    <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
