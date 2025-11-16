@include('layouts.header')

@yield('content')

<!-- Modal de confirmation pour suppression/archivage -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">
                    <i class="ti ti-alert-triangle me-2 text-warning"></i>Confirmation requise
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Annuler
                </button>
                <button type="button" class="btn btn-danger" id="confirmModalSubmit">
                    <i class="ti ti-check me-1"></i>Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
