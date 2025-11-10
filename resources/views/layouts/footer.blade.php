<!-- [ Main Content ] end -->
<footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
        <div class="row">
            <div class="col-sm my-1">
                <p class="m-0">MPOX Dashboard &#9829; crafted by SpectreCoding 💻👻. All rights reserved.</p>
            </div>
            <div class="col-auto my-1">
                <ul class="list-inline footer-link mb-0">
                    <li class="list-inline-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="list-inline-item"><a href="">Contact</a></li>
                    <li class="list-inline-item"><a href="">About</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!-- [Page Specific JS] start -->
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/dashboard-default.js') }}"></script>
<!-- [Page Specific JS] end -->

<!-- Required Js -->
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('assets/js/pcoded.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

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

@stack('scripts')

</body>
<!-- [Body] end -->
</html>
