<!doctype html>
<html lang="tr" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default"
    data-assets-path="/vuexy/assets/" data-template="vertical-menu-template-starter" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title', 'KBTS ARJ Sistem - Admin Panel')</title>
    <meta name="description" content="KBTS ARJ Sistem Admin Panel" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/vuexy/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="../../vuexy/assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="../../vuexy/assets/vendor/libs/node-waves/node-waves.css" />

    <link rel="stylesheet" href="../../vuexy/assets/vendor/libs/pickr/pickr-themes.css" />

    <link rel="stylesheet" href="../../vuexy/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../vuexy/assets/css/demo.css" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="../../vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <link rel="stylesheet" href="../../vuexy/assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="../../vuexy/assets/vendor/libs/swiper/swiper.css" />
    <link rel="stylesheet" href="../../vuexy/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../../vuexy/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../../vuexy/assets/vendor/fonts/flag-icons.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="../../vuexy/assets/vendor/css/pages/cards-advance.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../../vuexy/assets/vendor/libs/sweetalert2/sweetalert2.css" />

    
    @stack('styles')

    <!-- Helpers -->
    <script src="/vuexy/assets/vendor/js/helpers.js"></script>
    <script src="/vuexy/assets/vendor/js/template-customizer.js"></script>
    <script src="/vuexy/assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layouts.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('layouts.header')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('layouts.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="../../vuexy/assets/vendor/libs/jquery/jquery.js"></script>

    <script src="../../vuexy/assets/vendor/libs/popper/popper.js"></script>
    <script src="../../vuexy/assets/vendor/js/bootstrap.js"></script>
    <script src="../../vuexy/assets/vendor/libs/node-waves/node-waves.js"></script>

    <script src="../../vuexy/assets/vendor/libs/@algolia/autocomplete-js.js"></script>

    <script src="../../vuexy/assets/vendor/libs/pickr/pickr.js"></script>

    <script src="../../vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../../vuexy/assets/vendor/libs/hammer/hammer.js"></script>

    <script src="../../vuexy/assets/vendor/libs/i18n/i18n.js"></script>

    <script src="../../vuexy/assets/vendor/js/menu.js"></script>

    <!-- SweetAlert2 -->
    <script src="../../vuexy/assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../../vuexy/assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../../vuexy/assets/vendor/libs/swiper/swiper.js"></script>
    <script src="../../vuexy/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>    <!-- Main JS -->
    <script src="../../vuexy/assets/js/main.js"></script>
    
    <!-- Page JS -->
    <script src="../../vuexy/assets/js/dashboards-analytics.js"></script>

    <!-- Global SweetAlert Functions -->
    <script>
        // Global SweetAlert Success Function
        function showSwal(type, title, message, options = {}) {
            const defaultOptions = {
                icon: type,
                title: title,
                text: message,
                ...options
            };
            
            // Default options for different types
            switch(type) {
                case 'success':
                    defaultOptions.timer = defaultOptions.timer || 2000;
                    defaultOptions.showConfirmButton = defaultOptions.showConfirmButton !== undefined ? defaultOptions.showConfirmButton : false;
                    break;
                case 'error':
                    defaultOptions.showConfirmButton = defaultOptions.showConfirmButton !== undefined ? defaultOptions.showConfirmButton : true;
                    break;
                case 'warning':
                    defaultOptions.showCancelButton = defaultOptions.showCancelButton !== undefined ? defaultOptions.showCancelButton : true;
                    defaultOptions.confirmButtonColor = defaultOptions.confirmButtonColor || '#d33';
                    defaultOptions.cancelButtonColor = defaultOptions.cancelButtonColor || '#3085d6';
                    defaultOptions.confirmButtonText = defaultOptions.confirmButtonText || 'Evet';
                    defaultOptions.cancelButtonText = defaultOptions.cancelButtonText || 'İptal';
                    break;
            }
            
            return Swal.fire(defaultOptions);
        }

        // Shorthand functions
        function showSuccess(message, options = {}) {
            return showSwal('success', 'Başarılı!', message, options);
        }

        function showError(message, options = {}) {
            return showSwal('error', 'Hata!', message, options);
        }

        function showWarning(message, options = {}) {
            return showSwal('warning', 'Uyarı!', message, options);
        }

        function showInfo(message, options = {}) {
            return showSwal('info', 'Bilgi', message, options);
        }

        function showConfirm(message, options = {}) {
            return showSwal('warning', 'Emin misiniz?', message, {
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet',
                cancelButtonText: 'İptal',
                ...options
            });
        }
    </script>

    @stack('scripts')
</body>

</html>
