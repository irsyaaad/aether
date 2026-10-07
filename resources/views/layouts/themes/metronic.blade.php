<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="{{ url('') }}/">
    <title>@yield('title', config('app.name', 'Persada ERP'))</title>
    <meta charset="utf-8"/>
    <meta name="robots" content="follow, index"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content="@yield('meta_description', '')"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" href="{{ asset('images/logo_persada_duta.png') }}"/>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_persada_duta.png') }}"/>
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_persada_duta.png') }}"/>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet"/>

    @stack('styles')
</head>
<body class="antialiased flex h-full text-base text-foreground bg-background [--header-height:60px] [--sidebar-width:90px] bg-muted">

    <!-- Theme Mode -->
    <script>
        const defaultThemeMode = 'light';
        let themeMode;

        if (document.documentElement) {
            if (localStorage.getItem('kt-theme')) {
                themeMode = localStorage.getItem('kt-theme');
            } else if (document.documentElement.hasAttribute('data-kt-theme-mode')) {
                themeMode = document.documentElement.getAttribute('data-kt-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }

            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            document.documentElement.classList.add(themeMode);
        }
    </script>
    <!-- End of Theme Mode -->

    <!-- Page -->
    <div class="flex grow">
        <!-- Header (Mobile Only) -->
        @include('partials.header')
        <!-- End of Header -->

        <!-- Wrapper -->
        <div class="flex flex-col lg:flex-row grow pt-(--header-height) lg:pt-0">
            <!-- Sidebar -->
            @include('partials.sidebar')
            <!-- End of Sidebar -->

            <!-- Main -->
            <div class="flex flex-col grow rounded-xl bg-background border border-input lg:ms-(--sidebar-width) mt-0 lg:mt-5 m-5">
                <div class="flex flex-col grow kt-scrollable-y-auto lg:[--scrollbar-width:auto] pt-5" id="scrollable_content">
                    <main class="grow" role="content">
                        <!-- Toolbar -->
                        <div class="pb-5">
                            <div class="kt-container-fixed flex items-center justify-between flex-wrap gap-3">
                                <div class="flex items-center flex-wrap gap-1 lg:gap-5">
                                    <h1 class="font-medium text-base text-mono">
                                        @yield('page_title', config('app.name'))
                                    </h1>
                                    <div class="flex items-center flex-wrap gap-1 text-sm">
                                        @yield('breadcrumb')
                                    </div>
                                </div>
                                <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">


                                    @yield('toolbar_actions')
                                    <!-- Search -->
                                    <button class="group kt-btn kt-btn-ghost kt-btn-icon size-9 hover:bg-primary/10 hover:[&_i]:text-primary" data-kt-modal-toggle="#search_modal">
                                        <i class="ki-filled ki-magnifier text-lg group-hover:text-primary"></i>
                                    </button>
                                    <!-- End of Search -->
                                    <!-- Notifications -->
                                    <button class="kt-btn kt-btn-ghost kt-btn-icon size-9 hover:bg-primary/10 hover:[&_i]:text-primary" data-kt-drawer-toggle="#notifications_drawer">
                                        <i class="ki-filled ki-notification-status text-lg"></i>
                                    </button>
                                    <!-- End of Notifications -->
                                </div>
                            </div>
                        </div>
                        <!-- End of Toolbar -->

                        <!-- Content -->
                        @yield('content')
                    </main>
                </div>

                <!-- Footer -->
                @include('partials.footer')

            </div>
            <!-- End of Main -->
        </div>
        <!-- End of Wrapper -->
    </div>
    <!-- End of Page -->

    <!-- Modals & Drawers -->
    @include('partials.search-modal')
    @include('partials.notifications-drawer')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/core.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendors/ktui/ktui.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/widgets/general.js') }}"></script>

    <!-- Global Notifications & Form Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toast Configuration (for success only)
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Flash Messages
            @if(Session::has('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{!! addslashes(Session::get('success')) !!}"
                });
            @endif

            @if(Session::has('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{!! addslashes(Session::get('error')) !!}",
                    confirmButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'kt-btn kt-btn-primary',
                        popup: 'kt-modal-content',
                        header: 'kt-modal-header',
                        title: 'kt-modal-title'
                    },
                    buttonsStyling: false
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    html: "{!! implode('<br>', $errors->all()) !!}",
                    confirmButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'kt-btn kt-btn-primary',
                        popup: 'kt-modal-content',
                        header: 'kt-modal-header',
                        title: 'kt-modal-title'
                    },
                    buttonsStyling: false
                });
            @endif

            // Helper: restore submit buttons to original state
            window.restoreSubmitButtons = function(form) {
                if (!form) return;
                const buttons = form.querySelectorAll('button[type="submit"]');
                buttons.forEach(function(btn) {
                    btn.disabled = false;
                    btn.classList.remove('disabled', 'opacity-50', 'cursor-not-allowed');
                    btn.style.minWidth = '';
                    if (btn.dataset.originalHtml) {
                        btn.innerHTML = btn.dataset.originalHtml;
                        delete btn.dataset.originalHtml;
                    }
                });
            };

            // Global Form Submit Handler for Spinner
            document.querySelectorAll('form').forEach(function(form) {
                if(form.getAttribute('data-no-spinner') === 'true') return;

                form.addEventListener('submit', function(e) {
                    if (!form.checkValidity()) return;

                    const submitButtons = form.querySelectorAll('button[type="submit"]');
                    submitButtons.forEach(function(btn) {
                        if(btn.disabled) {
                            e.preventDefault();
                            return;
                        }
                        btn.dataset.originalHtml = btn.innerHTML;
                        btn.disabled = true;
                        btn.classList.add('disabled', 'opacity-50', 'cursor-not-allowed');
                        
                        const currentWidth = btn.offsetWidth;
                        if(currentWidth > 0) {
                            btn.style.minWidth = currentWidth + 'px';
                        }
                        
                        btn.innerHTML = '<div class="kt-spinner"></div> <span class="ml-2">Menyimpan...</span>';
                    });
                });
            });
        });

        // Global AJAX error handler
        window.handleAjaxError = function(xhr, form) {
            let msg = 'Terjadi kesalahan pada server.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errs = Object.values(xhr.responseJSON.errors).flat();
                msg = errs.join('<br>');
            }

            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: msg,
                confirmButtonText: 'Tutup',
                customClass: {
                    confirmButton: 'kt-btn kt-btn-primary',
                    popup: 'kt-modal-content',
                    header: 'kt-modal-header',
                    title: 'kt-modal-title'
                },
                buttonsStyling: false
            });

            if (form) window.restoreSubmitButtons(form);
        };
    </script>

    @stack('scripts')
</body>
</html>
