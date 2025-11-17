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
    }

    @media (max-width: 575.98px) {
        .notification-dropdown {
            max-width: calc(100vw - 2rem);
            width: calc(100vw - 2rem);
            left: auto !important;
            right: 1rem !important;
        }

        .notification-list {
            max-height: 60vh !important;
        }

        #notificationsList .dropdown-item {
            padding: 0.75rem 0.75rem !important;
            font-size: 0.875rem;
        }

        #notificationsList .dropdown-item p {
            font-size: 0.875rem !important;
            margin-bottom: 0.25rem !important;
        }

        #notificationsList .dropdown-item small {
            font-size: 0.75rem !important;
        }

        .dropdown-header h6 {
            font-size: 0.875rem;
        }

        .dropdown-header .btn {
            font-size: 0.75rem;
        }

        #notificationBadge {
            font-size: 10px !important;
            padding: 3px 6px !important;
            min-width: 18px !important;
            height: 18px !important;
            top: -6px !important;
            right: -4px !important;
        }
    }

    @media (max-width: 360px) {
        .notification-dropdown {
            max-width: calc(100vw - 1rem);
            width: calc(100vw - 1rem);
            right: 0.5rem !important;
        }
    }
</style>

</body>
<!-- [Body] end -->
</html>
