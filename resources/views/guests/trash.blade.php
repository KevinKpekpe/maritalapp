@extends('app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="mb-3 mb-sm-0">Corbeille - Invités</h5>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <a href="{{ route('guests.index') }}" class="btn btn-outline-primary" title="Retour à la liste">
                            <i class="ti ti-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3 px-3 px-md-4">
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

                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="ti ti-info-circle me-2"></i>
                    <span>Les éléments archivés peuvent être restaurés ou supprimés définitivement.</span>
                </div>

                <div id="guest-trash-table-container">
                    @include('guests.partials.trash-table', ['guests' => $guests])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
