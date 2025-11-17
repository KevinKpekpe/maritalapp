<!-- [ Main Content ] end -->
<footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
        <div class="row">
            <div class="col-sm my-1 text-center">
                <p class="m-0">Conçu et developpé par <span class="text-primary">Spectre Coding</span> 💻👻.</p>
                <p> Tous droits reservés.</p>
            </div>
            {{-- <div class="col-auto my-1">
                <ul class="list-inline footer-link mb-0">
                    <li class="list-inline-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="list-inline-item"><a href="">Contact</a></li>
                    <li class="list-inline-item"><a href="">About</a></li>
                </ul>
            </div> --}}
        </div>
    </div>
</footer>

<!-- [Page Specific JS] start -->
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
@stack('scripts')
<script src="{{ asset('assets/js/pages/dashboard-default.js') }}"></script>
<!-- [Page Specific JS] end -->

<!-- Required Js -->
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('assets/js/pcoded.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

<!-- Modal de confirmation -->
<script>
(function() {
    // Attendre que le DOM et Bootstrap soient chargés
    function initConfirmModal() {
        const confirmModal = document.getElementById('confirmModal');
        if (!confirmModal || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            // Réessayer après un court délai si Bootstrap n'est pas encore chargé
            setTimeout(initConfirmModal, 100);
            return;
        }

        const bsModal = new bootstrap.Modal(confirmModal);
        const modalTitle = document.getElementById('confirmModalLabel');
        const modalMessage = document.getElementById('confirmModalMessage');
        let modalSubmit = document.getElementById('confirmModalSubmit');

        // Fonction pour afficher le modal de confirmation
        window.showConfirmModal = function(options) {
            const {
                title = 'Confirmation requise',
                message = 'Êtes-vous sûr de vouloir effectuer cette action ?',
                confirmText = 'Confirmer',
                confirmClass = 'btn-danger',
                icon = 'ti-alert-triangle',
                iconColor = 'text-warning',
                onSubmit = null,
                form = null
            } = options;

            // Mettre à jour le titre
            if (title && modalTitle) {
                modalTitle.innerHTML = `<i class="ti ${icon} me-2 ${iconColor}"></i>${title}`;
            }

            // Mettre à jour le message
            if (modalMessage) {
                modalMessage.textContent = message;
            }

            // Gérer la soumission
            const handleSubmit = () => {
                bsModal.hide();

                // Attendre que le modal soit complètement fermé avant de soumettre
                confirmModal.addEventListener('hidden.bs.modal', function onSubmitComplete() {
                    confirmModal.removeEventListener('hidden.bs.modal', onSubmitComplete);

                    if (onSubmit && typeof onSubmit === 'function') {
                        onSubmit();
                    } else if (form && form.nodeType === 1) {
                        // Vérifier que c'est bien un élément HTML
                        form.submit();
                    }
                }, { once: true });
            };

            // Supprimer les anciens listeners en clonant le bouton
            if (modalSubmit) {
                const newSubmit = modalSubmit.cloneNode(true);
                modalSubmit.parentNode.replaceChild(newSubmit, modalSubmit);
                modalSubmit = newSubmit;

                // Mettre à jour le bouton de confirmation
                modalSubmit.className = `btn ${confirmClass}`;
                modalSubmit.innerHTML = `<i class="ti ti-check me-1"></i>${confirmText}`;

                // Ajouter le nouveau listener
                modalSubmit.addEventListener('click', handleSubmit);
            }

            // Afficher le modal
            bsModal.show();
        };

        // Gérer les formulaires avec data-confirm
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.hasAttribute('data-confirm')) {
                e.preventDefault();
                e.stopPropagation();

                let confirmOptions = {};
                try {
                    confirmOptions = form.dataset.confirmOptions ? JSON.parse(form.dataset.confirmOptions) : {};
                } catch (err) {
                    console.error('Erreur lors du parsing des options:', err);
                }

                const defaultOptions = {
                    title: confirmOptions.title || 'Confirmation requise',
                    message: confirmOptions.message || form.getAttribute('data-confirm'),
                    confirmText: confirmOptions.confirmText || 'Confirmer',
                    confirmClass: confirmOptions.confirmClass || 'btn-danger',
                    icon: confirmOptions.icon || 'ti-alert-triangle',
                    iconColor: confirmOptions.iconColor || 'text-warning',
                    form: form // Passer le formulaire directement dans les options
                };

                if (window.showConfirmModal) {
                    window.showConfirmModal(defaultOptions);
                } else {
                    // Fallback vers confirm() si le modal n'est pas disponible
                    if (confirm(defaultOptions.message)) {
                        form.submit();
                    }
                }
            }
        }, true); // Utiliser capture pour intercepter avant les autres handlers
    }

    // Initialiser quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initConfirmModal);
    } else {
        initConfirmModal();
    }
})();
</script>

<script>
    layout_change('light');
</script>

<script>
    change_box_container('false');
</script>

<script>
    layout_rtl_change('false');
</script>

<script>
    preset_change("preset-1");
</script>

<script>
    font_change("Public-Sans");
</script>

<!-- Notification System -->
<script>
    (function() {
        let notificationInterval = null;

        // Fonction pour charger le nombre de notifications non lues
        function loadNotificationCount() {
            // Vérifier si le badge existe avant de faire la requête
            const badge = document.getElementById('notificationBadge');
            if (!badge) {
                return; // Pas sur une page avec notifications
            }

            fetch('{{ route("notifications.count") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur HTTP: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                // Re-vérifier que le badge existe toujours
                const currentBadge = document.getElementById('notificationBadge');
                if (!currentBadge) {
                    return;
                }
                const count = parseInt(data.count) || 0;
                if (count > 0) {
                    const countText = count > 99 ? '99+' : count.toString();
                    currentBadge.textContent = countText;
                    currentBadge.innerHTML = countText;
                    currentBadge.style.display = 'inline-flex';
                    currentBadge.style.visibility = 'visible';
                    currentBadge.style.opacity = '1';
                } else {
                    currentBadge.style.display = 'none';
                }
            })
            .catch(error => {
                // Ignorer les erreurs silencieusement si le badge n'existe pas
                const currentBadge = document.getElementById('notificationBadge');
                if (currentBadge) {
                    console.error('Erreur lors du chargement du compteur:', error);
                }
            });
        }

        // Fonction pour charger les notifications
        function loadNotifications() {
            const loader = document.getElementById('notificationLoader');
            const list = document.getElementById('notificationsList');
            const noNotifications = document.getElementById('noNotifications');
            const markAllBtn = document.getElementById('markAllReadBtn');
            const footer = document.getElementById('notificationFooter');

            // Vérifier que tous les éléments existent
            if (!loader || !list || !noNotifications || !markAllBtn || !footer) {
                // Les éléments n'existent pas, probablement sur une page sans header
                return;
            }

            if (loader.parentElement) {
                loader.parentElement.style.display = 'block';
            }
            noNotifications.style.display = 'none';

            fetch('{{ route("notifications.index") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (loader.parentElement) {
                    loader.parentElement.style.display = 'none';
                }
                list.innerHTML = '';

                if (data.notifications && data.notifications.length > 0) {
                    const unreadCount = data.notifications.filter(n => !n.read_at).length;

                    if (unreadCount > 0) {
                        markAllBtn.style.display = 'block';
                    } else {
                        markAllBtn.style.display = 'none';
                    }

                    data.notifications.forEach(notification => {
                        const item = document.createElement('a');
                        item.href = '{{ route("guests.index") }}';
                        item.className = 'dropdown-item' + (notification.read_at ? '' : ' bg-light');
                        item.style.cursor = 'pointer';

                        const icon = notification.type === 'rsvp_confirmed' ? 'ti-check' : 'ti-bell';
                        const date = new Date(notification.created_at);
                        const timeAgo = formatTimeAgo(date);

                        item.innerHTML = `
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="ti ${icon} text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-2" style="min-width: 0;">
                                    <p class="mb-1 ${notification.read_at ? '' : 'fw-bold'} text-break">${escapeHtml(notification.message)}</p>
                                    <small class="text-muted d-block">${timeAgo}</small>
                                </div>
                                ${!notification.read_at ? '<span class="badge bg-primary rounded-pill flex-shrink-0 ms-2">Nouveau</span>' : ''}
                            </div>
                        `;

                        if (!notification.read_at) {
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                markAsRead(notification.id, item);
                                window.location.href = '{{ route("guests.index") }}';
                            });
                        } else {
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                window.location.href = '{{ route("guests.index") }}';
                            });
                        }

                        list.appendChild(item);
                    });

                    footer.style.display = 'block';
                    noNotifications.style.display = 'none';
                } else {
                    footer.style.display = 'none';
                    noNotifications.style.display = 'block';
                    markAllBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des notifications:', error);
                if (loader && loader.parentElement) {
                    loader.parentElement.style.display = 'none';
                }
                if (list) {
                    list.innerHTML = '<div class="text-center p-3 text-danger"><p class="mb-0">Erreur lors du chargement</p></div>';
                }
            });
        }

        // Fonction pour marquer une notification comme lue
        function markAsRead(notificationId, element) {
            fetch(`{{ url('/notifications') }}/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    element.classList.remove('bg-light');
                    const badgeElement = element.querySelector('.badge');
                    if (badgeElement) {
                        badgeElement.remove();
                    }
                    loadNotificationCount();
                }
            })
            .catch(error => console.error('Erreur lors de la mise à jour:', error));
        }

        // Fonction pour marquer toutes les notifications comme lues
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                fetch('{{ route("notifications.mark-all-as-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                        loadNotificationCount();
                    }
                })
                .catch(error => console.error('Erreur:', error));
            });
        }

        // Fonction pour formater la date
        function formatTimeAgo(date) {
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);

            if (diffInSeconds < 60) return 'À l\'instant';
            if (diffInSeconds < 3600) return `Il y a ${Math.floor(diffInSeconds / 60)} min`;
            if (diffInSeconds < 86400) return `Il y a ${Math.floor(diffInSeconds / 3600)} h`;
            if (diffInSeconds < 604800) return `Il y a ${Math.floor(diffInSeconds / 86400)} j`;

            return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        // Fonction pour échapper le HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Attendre que le DOM soit chargé
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initNotifications);
        } else {
            initNotifications();
        }

        function initNotifications() {
            // Vérifier si les éléments de notification existent (seulement sur les pages avec header)
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationBadge = document.getElementById('notificationBadge');
            const notificationLoader = document.getElementById('notificationLoader');

            // Si les éléments de notification n'existent pas, ne pas initialiser
            // C'est normal sur les pages publiques (invitations) qui n'ont pas de header
            if (!notificationDropdown || !notificationBadge || !notificationLoader) {
                return; // Quitter silencieusement sans erreur
            }

            // Charger les notifications au clic sur le dropdown
            notificationDropdown.addEventListener('click', function() {
                loadNotifications();
            });

            // Centrer le dropdown sur mobile avec une approche robuste
            const dropdownMenu = document.getElementById('notificationDropdownMenu');
            if (dropdownMenu && notificationDropdown) {
                function forceCenterDropdown() {
                    if (window.innerWidth <= 575.98 && dropdownMenu.classList.contains('show')) {
                        const viewportWidth = window.innerWidth;
                        const dropdownWidth = dropdownMenu.offsetWidth || Math.min(viewportWidth - 24, 400);
                        const leftPosition = Math.max(12, (viewportWidth - dropdownWidth) / 2);

                        // Forcer le centrage avec position fixed
                        dropdownMenu.style.setProperty('position', 'fixed', 'important');
                        dropdownMenu.style.setProperty('left', leftPosition + 'px', 'important');
                        dropdownMenu.style.setProperty('right', 'auto', 'important');
                        dropdownMenu.style.setProperty('transform', 'none', 'important');
                        dropdownMenu.style.setProperty('margin-left', '0', 'important');
                        dropdownMenu.style.setProperty('margin-right', '0', 'important');
                        dropdownMenu.style.setProperty('z-index', '1050', 'important');

                        // Positionner verticalement en dessous du bouton
                        const buttonRect = notificationDropdown.getBoundingClientRect();
                        const topPosition = buttonRect.bottom + 8;
                        dropdownMenu.style.setProperty('top', topPosition + 'px', 'important');
                    }
                }

                // Centrer quand le dropdown s'ouvre
                notificationDropdown.addEventListener('shown.bs.dropdown', function() {
                    if (window.innerWidth <= 575.98) {
                        // Plusieurs tentatives pour s'assurer que le centrage est appliqué
                        requestAnimationFrame(function() {
                            forceCenterDropdown();
                            setTimeout(forceCenterDropdown, 50);
                            setTimeout(forceCenterDropdown, 100);
                            setTimeout(forceCenterDropdown, 200);
                        });
                    }
                });

                // Observer les changements de style pour maintenir le centrage
                if (window.MutationObserver) {
                    const observer = new MutationObserver(function(mutations) {
                        if (window.innerWidth <= 575.98 && dropdownMenu.classList.contains('show')) {
                            forceCenterDropdown();
                        }
                    });
                    observer.observe(dropdownMenu, {
                        attributes: true,
                        attributeFilter: ['style', 'class'],
                        childList: false,
                        subtree: false
                    });
                }

                // Re-centrer lors du redimensionnement
                window.addEventListener('resize', function() {
                    if (dropdownMenu.classList.contains('show') && window.innerWidth <= 575.98) {
                        forceCenterDropdown();
                    }
                });
            }

            // Charger le compteur au chargement de la page
            loadNotificationCount();

            // Recharger le compteur toutes les 30 secondes
            notificationInterval = setInterval(loadNotificationCount, 30000);
        }
    })();
</script>

<style>
    /* Assurer que les parents ne coupent pas le badge */
    .pc-h-item {
        overflow: visible !important;
    }

    #notificationDropdown {
        overflow: visible !important;
    }

    .pc-header,
    .pc-header .header-wrapper,
    .pc-header .ms-auto,
    .pc-header .ms-auto ul.list-unstyled {
        overflow: visible !important;
    }

    #notificationBadge {
        font-size: 11px !important;
        padding: 4px 7px !important;
        min-width: 20px !important;
        height: 20px !important;
        text-align: center !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
        box-sizing: border-box !important;
        background-color: #dc3545 !important;
        border: none !important;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.8) !important;
        position: absolute !important;
        top: -8px !important;
        right: -6px !important;
        z-index: 1000 !important;
        pointer-events: none !important;
    }

    #notificationBadge * {
        color: #ffffff !important;
    }

    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    #notificationsList .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    #notificationsList .dropdown-item.bg-light {
        background-color: #f8f9fa !important;
    }

    /* Styles pour les notifications sur mobile */
    .notification-dropdown {
        max-width: 350px;
        width: 350px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
    }

    @media (max-width: 991.98px) {
        .notification-dropdown {
            max-width: 90vw !important;
            width: 90vw !important;
            max-width: 400px !important;
        }

        .notification-dropdown::before,
        .notification-dropdown::after {
            display: none !important;
        }
    }

    @media (max-width: 575.98px) {
        .notification-dropdown {
            max-width: calc(100vw - 1.5rem) !important;
            width: calc(100vw - 1.5rem) !important;
            /* Centrer avec left et margin négatif */
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* S'assurer que le dropdown reste centré quand il est visible */
        .notification-dropdown.show,
        .notification-dropdown.dropdown-menu-end.show {
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .notification-list {
            max-height: 55vh !important;
        }

        #notificationsList .dropdown-item {
            padding: 0.625rem 0.75rem !important;
            font-size: 0.8125rem;
            white-space: normal !important;
            word-wrap: break-word !important;
        }

        #notificationsList .dropdown-item p {
            font-size: 0.8125rem !important;
            margin-bottom: 0.25rem !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            line-height: 1.4 !important;
        }

        #notificationsList .dropdown-item small {
            font-size: 0.6875rem !important;
            display: block;
            margin-top: 0.25rem;
        }

        .dropdown-header {
            padding: 0.625rem 0.75rem !important;
        }

        .dropdown-header h6 {
            font-size: 0.8125rem;
            margin: 0;
        }

        .dropdown-header .btn {
            font-size: 0.6875rem;
            white-space: nowrap;
            padding: 0.125rem 0.25rem !important;
        }

        #notificationBadge {
            font-size: 9px !important;
            padding: 2px 5px !important;
            min-width: 16px !important;
            height: 16px !important;
            top: -5px !important;
            right: -3px !important;
        }

        #notificationsList .dropdown-item .d-flex {
            flex-wrap: wrap;
            gap: 0.375rem;
        }

        #notificationsList .dropdown-item .badge {
            margin-left: auto;
            flex-shrink: 0;
            font-size: 0.625rem !important;
            padding: 0.125rem 0.375rem !important;
        }

        #notificationsList .dropdown-item .flex-shrink-0 {
            margin-right: 0.5rem;
        }
    }

    @media (max-width: 400px) {
        .notification-dropdown {
            max-width: calc(100vw - 0.5rem) !important;
            width: calc(100vw - 0.5rem) !important;
        }

        .notification-list {
            max-height: 50vh !important;
        }
    }

    @media (max-width: 360px) {
        .notification-dropdown {
            max-width: calc(100vw - 0.25rem) !important;
            width: calc(100vw - 0.25rem) !important;
        }

        .dropdown-header {
            padding: 0.5rem 0.625rem !important;
        }

        .dropdown-header h6 {
            font-size: 0.75rem;
        }

        .dropdown-header .btn {
            font-size: 0.625rem;
        }

        #notificationsList .dropdown-item {
            padding: 0.5rem 0.625rem !important;
            font-size: 0.75rem;
        }

        #notificationsList .dropdown-item p {
            font-size: 0.75rem !important;
        }

        #notificationsList .dropdown-item small {
            font-size: 0.625rem !important;
        }

        .notification-list {
            max-height: 45vh !important;
        }
    }

    /* Ajustements du header pour très petits écrans */
    @media (max-width: 400px) {
        .pc-header .ms-auto ul.list-unstyled {
            gap: 0.25rem !important;
        }

        .pc-head-link {
            padding: 0.5rem 0.375rem !important;
        }

        .pc-head-link i {
            font-size: 1.125rem !important;
        }

        .header-user-profile .pc-head-link span {
            display: none !important;
        }
    }

    @media (max-width: 360px) {
        .pc-header .ms-auto ul.list-unstyled {
            gap: 0.125rem !important;
        }

        .pc-head-link {
            padding: 0.5rem 0.25rem !important;
        }

        .pc-head-link i {
            font-size: 1rem !important;
        }

        #notificationDropdown {
            margin-right: 0.25rem !important;
        }
    }
</style>

</body>
<!-- [Body] end -->
</html>
