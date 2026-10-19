<!-- Header -->
<header class="flex lg:hidden items-center fixed z-10 top-0 start-0 end-0 shrink-0 bg-muted h-(--header-height)" id="header">
    <!-- Container -->
    <div class="kt-container-fixed flex items-center justify-between flex-wrap gap-3">
        <a href="{{ route('dashboard') }}">
            <img class="h-8 w-auto object-contain" src="{{ asset('images/logo_aether.png') }}" alt="Aether"/>
        </a>
        <button class="kt-btn kt-btn-icon kt-btn-ghost -me-1" data-kt-drawer-toggle="#sidebar">
            <i class="ki-filled ki-menu">
            </i>
        </button>
    </div>
    <!-- End of Container -->
</header>
<!-- End of Header -->
