<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- ApexCharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- jsVectorMap -->
<script src="{{ asset('assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

<!-- Swiper -->
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

<!-- Dashboard init -->
<script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>

<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
    // Top function
    function topFunction() {
        document.documentElement.scrollTop = 0;
    }

    // Helper global pour modale de confirmation
    window.confirmAction = function(message, onConfirm, title = 'Confirmation', buttonText = 'Confirmer', buttonClass = 'btn-danger') {
        const modalElement = document.getElementById('confirmActionModal');
        if (!modalElement) return false;

        document.getElementById('confirmActionTitle').innerText = title;
        document.getElementById('confirmActionMessage').innerText = message;
        
        const confirmBtn = document.getElementById('confirmActionButton');
        confirmBtn.innerText = buttonText;
        confirmBtn.className = `btn w-sm ${buttonClass}`;

        // Cloner le bouton pour nettoyer d'éventuels anciens écouteurs d'événements
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        const modal = new bootstrap.Modal(modalElement);
        
        newConfirmBtn.addEventListener('click', function() {
            modal.hide();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });

        modal.show();
    };
</script>