<!-- Sidebar -->
<div class="fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 bg-muted [--kt-drawer-enable:true] lg:[--kt-drawer-enable:false]"
     data-kt-drawer="true"
     data-kt-drawer-class="kt-drawer kt-drawer-start flex"
     id="sidebar">
    <!-- Sidebar Header -->
    <div class="hidden lg:flex items-center justify-center shrink-0 pt-8 pb-3.5" id="sidebar_header">
        <a href="{{ route('dashboard') }}">
            <img class="max-h-12 w-auto object-contain px-2" src="{{ asset('images/logo_persada_duta.png') }}" alt="Persada Duta Express"/>
        </a>
    </div>
    <!-- End of Sidebar Header -->

    <!-- Sidebar Menu Wrapper -->
    <div class="kt-scrollable-y-hover grow gap-2.5 shrink-0 flex items-center pt-5 lg:pt-0 ps-3 pe-3 lg:pe-0 flex-col"
         data-kt-scrollable="true"
         data-kt-scrollable-dependencies="#sidebar_header,#sidebar_footer"
         data-kt-scrollable-height="auto"
         data-kt-scrollable-offset="80px"
         data-kt-scrollable-wrappers="#sidebar_menu_wrapper"
         id="sidebar_menu_wrapper">
        <!-- Sidebar Menu -->
        <div class="kt-menu flex flex-col gap-2.5 grow"
             data-kt-menu="true"
             id="sidebar_menu">
            @include('partials.sidebar-menu')
        </div>
        <!-- End of Sidebar Menu -->
    </div>
    <!-- End of Sidebar Menu Wrapper -->

    <!-- Sidebar Footer -->
    <div class="flex flex-col gap-5 items-center shrink-0 pb-4" id="sidebar_footer">
        <div class="flex flex-col gap-1.5">
            <!-- Chat -->
            <button class="kt-btn kt-btn-ghost kt-btn-icon size-9 hover:bg-background hover:[&_i]:text-primary" data-kt-drawer-toggle="#chat_drawer">
                <i class="ki-filled ki-messages text-lg"></i>
            </button>

            <!-- Chat Drawer -->
            <div class="hidden kt-drawer kt-drawer-end card flex-col max-w-[90%] w-[450px] top-5 bottom-5 end-5 rounded-xl border border-border"
                 data-kt-drawer="true"
                 data-kt-drawer-container="body"
                 id="chat_drawer">
                <div>
                    <div class="flex items-center justify-between gap-2.5 text-sm text-mono font-semibold px-5 py-3.5">
                        Chat
                        <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-drawer-dismiss="true">
                            <i class="ki-filled ki-cross"></i>
                        </button>
                    </div>
                    <div class="border-b border-b-border"></div>
                </div>
                <div class="flex items-center justify-center grow text-sm text-secondary-foreground p-10">
                    No conversations yet
                </div>
            </div>
            <!-- End of Chat Drawer -->
            <!-- End of Chat -->

            <!-- Apps -->
            <div data-kt-dropdown="true"
                 data-kt-dropdown-offset="10px, 10px"
                 data-kt-dropdown-offset-rtl="-10px, 10px"
                 data-kt-dropdown-placement="bottom-start"
                 data-kt-dropdown-placement-rtl="bottom-end">
                <button class="kt-btn kt-btn-ghost kt-btn-icon size-9 hover:bg-background hover:[&_i]:text-primary kt-dropdown-open:bg-background kt-dropdown-open:[&_i]:text-primary"
                        data-kt-dropdown-toggle="true">
                    <i class="ki-filled ki-element-11 text-lg"></i>
                </button>
                <div class="kt-dropdown-menu p-0 w-screen max-w-[320px]" data-kt-dropdown-menu="true">
                    <div class="flex items-center justify-between gap-2.5 text-xs text-secondary-foreground font-medium px-5 py-3 border-b border-b-border">
                        <span>Apps</span>
                        <span>Enabled</span>
                    </div>
                    <div class="p-10 text-sm text-secondary-foreground text-center">
                        No apps configured
                    </div>
                </div>
            </div>
            <!-- End of Apps -->
        </div>

        <!-- User -->
        <div data-kt-dropdown="true"
             data-kt-dropdown-offset="10px, 10px"
             data-kt-dropdown-offset-rtl="-20px, 10px"
             data-kt-dropdown-placement="bottom-start"
             data-kt-dropdown-placement-rtl="bottom-end"
             data-kt-dropdown-trigger="click">
            <div class="cursor-pointer shrink-0" data-kt-dropdown-toggle="true">
                <img alt="" class="size-9 rounded-lg shrink-0" src="assets/media/avatars/300-2.png"/>
            </div>
            <div class="kt-dropdown-menu w-[250px]" data-kt-dropdown-menu="true">
                <div class="flex items-center justify-between px-2.5 py-1.5 gap-1.5">
                    <div class="flex items-center gap-2">
                        <img alt="" class="size-9 shrink-0 rounded-full border-2 border-green-500" src="assets/media/avatars/300-2.png"/>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-sm text-foreground font-semibold leading-none">{{ Auth::user()->nm_user ?? 'User' }}</span>
                            <span class="text-xs text-secondary-foreground font-medium leading-none" title="{{ Auth::user()->email ?? 'user@example.com' }}">
                                {{ Auth::user()->active_role_name ?? (session('role')['nm_role'] ?? 'User') }}
                            </span>
                        </div>
                    </div>
                    <span class="kt-badge kt-badge-sm kt-badge-primary kt-badge-outline">{{ Auth::user()->active_role_name ?? 'Pro' }}</span>
                </div>
                <ul class="kt-dropdown-menu-sub">
                    <li><div class="kt-dropdown-menu-separator"></div></li>
                    <li>
                        <a class="kt-dropdown-menu-link" href="{{ route('dashboard') }}">
                            <i class="ki-filled ki-profile-circle"></i>
                            My Profile
                        </a>
                    </li>
                    <li>
                        <a class="kt-dropdown-menu-link" href="{{ url('change-password') }}">
                            <i class="ki-filled ki-lock"></i>
                            Change Password
                        </a>
                    </li>
                    <li>
                        <div class="kt-dropdown-menu-separator"></div>
                    </li>
                </ul>
                <div class="px-2.5 pt-1.5 mb-2.5 flex flex-col gap-3.5">
                    <div class="flex items-center gap-2 justify-between">
                        <span class="flex items-center gap-2">
                            <i class="ki-filled ki-moon text-base text-muted-foreground"></i>
                            <span class="font-medium text-2sm">Dark Mode</span>
                        </span>
                        <input class="kt-switch" data-kt-theme-switch-state="dark" data-kt-theme-switch-toggle="true" name="check" type="checkbox" value="1"/>
                    </div>
                    <a class="kt-btn kt-btn-outline justify-center w-full" href="{{ url('auth/logout') }}">
                        Log out
                    </a>
                </div>
            </div>
        </div>
        <!-- End of User -->
    </div>
    <!-- End of Sidebar Footer -->
</div>
<!-- End of Sidebar -->

<script>
    (function () {
        function scrollMetronicActiveMenu() {
            var wrapper = document.getElementById('sidebar_menu_wrapper');
            if (!wrapper) return;
            var activeItem = wrapper.querySelector('.kt-menu-item.active, .active');
            if (!activeItem) return;

            var containerRect = wrapper.getBoundingClientRect();
            var itemRect = activeItem.getBoundingClientRect();
            var relativeTop = (itemRect.top - containerRect.top) + wrapper.scrollTop;
            var targetScrollTop = relativeTop - (wrapper.clientHeight / 2) + (activeItem.clientHeight / 2);
            var maxScrollTop = wrapper.scrollHeight - wrapper.clientHeight;
            var clampedScrollTop = Math.max(0, Math.min(targetScrollTop, maxScrollTop));

            if (typeof wrapper.scrollTo === 'function') {
                wrapper.scrollTo({ top: clampedScrollTop, behavior: 'smooth' });
            } else {
                wrapper.scrollTop = clampedScrollTop;
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(scrollMetronicActiveMenu, 100);
            });
        } else {
            setTimeout(scrollMetronicActiveMenu, 100);
        }
    })();
</script>
