/**
 * LogisPro Theme Interactive Utilities & UI Handlers
 */
document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize Lucide Icons
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
    }

    // 2. Sidebar Toggle & Collapse Handlers (Mobile & Desktop)
    const sidebarToggleBtn = document.getElementById('logis-sidebar-toggle');
    const sidebarCloseBtn = document.getElementById('logis-sidebar-close');
    const sidebar = document.getElementById('logis-sidebar');
    const sidebarBackdrop = document.getElementById('logis-sidebar-backdrop');

    // Sync initial desktop collapsed state
    if (sidebar && window.innerWidth >= 1024) {
        try {
            if (localStorage.getItem('sidebar_collapsed') === 'true' || document.documentElement.classList.contains('sidebar-collapsed')) {
                sidebar.classList.add('is-collapsed');
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch (e) {}
    }

    function openMobileSidebar() {
        if (sidebar) sidebar.classList.remove('-translate-x-full');
        if (sidebarBackdrop) sidebarBackdrop.classList.remove('hidden');
        if (typeof scrollActiveSidebarMenu === 'function') {
            setTimeout(function () {
                scrollActiveSidebarMenu({ smooth: true });
            }, 150);
        }
    }

    function closeMobileSidebar() {
        if (sidebar) sidebar.classList.add('-translate-x-full');
        if (sidebarBackdrop) sidebarBackdrop.classList.add('hidden');
    }

    function toggleSidebar() {
        if (!sidebar) return;

        if (window.innerWidth < 1024) {
            if (sidebar.classList.contains('-translate-x-full')) {
                openMobileSidebar();
            } else {
                closeMobileSidebar();
            }
        } else {
            const isCollapsed = sidebar.classList.toggle('is-collapsed');
            if (isCollapsed) {
                document.documentElement.classList.add('sidebar-collapsed');
            } else {
                document.documentElement.classList.remove('sidebar-collapsed');
            }
            try {
                localStorage.setItem('sidebar_collapsed', isCollapsed ? 'true' : 'false');
            } catch (e) {}

            setTimeout(function () {
                window.dispatchEvent(new Event('resize'));
            }, 320);
        }
    }

    function handleSidebarClose() {
        if (!sidebar) return;

        if (window.innerWidth < 1024) {
            closeMobileSidebar();
        } else {
            sidebar.classList.add('is-collapsed');
            document.documentElement.classList.add('sidebar-collapsed');
            try {
                localStorage.setItem('sidebar_collapsed', 'true');
            } catch (e) {}

            setTimeout(function () {
                window.dispatchEvent(new Event('resize'));
            }, 320);
        }
    }

    document.querySelectorAll('#logis-sidebar-toggle, [data-logis-sidebar-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            toggleSidebar();
        });
    });

    document.querySelectorAll('#logis-sidebar-close, [data-logis-sidebar-close]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            handleSidebarClose();
        });
    });

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', closeMobileSidebar);
    }

    // Keyboard ESC to close sidebar drawer on mobile
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && window.innerWidth < 1024 && sidebar && !sidebar.classList.contains('-translate-x-full')) {
            closeMobileSidebar();
        }
    });

    // 3. Dropdown Toggles (data-logis-dropdown)
    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('[data-logis-dropdown-toggle]');
        if (toggleBtn) {
            e.stopPropagation();
            const targetId = toggleBtn.getAttribute('data-logis-dropdown-toggle');
            const targetMenu = document.getElementById(targetId);
            
            // Close other open dropdowns
            document.querySelectorAll('[data-logis-dropdown-menu]').forEach(function(menu) {
                if (menu !== targetMenu) menu.classList.add('hidden');
            });

            if (targetMenu) {
                targetMenu.classList.toggle('hidden');
            }
            return;
        }

        // Close dropdown when clicking outside
        if (!e.target.closest('[data-logis-dropdown-menu]')) {
            document.querySelectorAll('[data-logis-dropdown-menu]').forEach(function(menu) {
                menu.classList.add('hidden');
            });
        }
    });

    // 4. Slide-over Drawer Handler
    window.openLogisDrawer = function (drawerId) {
        const drawer = document.getElementById(drawerId);
        if (drawer) {
            drawer.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    };

    window.closeLogisDrawer = function (drawerId) {
        const drawer = document.getElementById(drawerId);
        if (drawer) {
            drawer.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    };

    // Close drawers with data-logis-drawer-close
    document.querySelectorAll('[data-logis-drawer-close]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const drawer = btn.closest('[data-logis-drawer]');
            if (drawer) {
                drawer.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });

    // 5. Global Form Submit Spinner for LogisPro
    document.querySelectorAll('form').forEach(function (form) {
        if (form.getAttribute('data-no-spinner') === 'true') return;

        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) return;

            const submitButtons = form.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(function (btn) {
                if (btn.disabled) {
                    e.preventDefault();
                    return;
                }
                btn.dataset.originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
                btn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Menyimpan...';
            });
        });
    });

    // 6. Global DataTables Processing / Loading Indicator
    function initDataTablesLoading() {
        var dtSpinnerHtml = '<div class="dt-spinner-only" style="display: flex !important; align-items: center !important; justify-content: center !important; pointer-events: none !important; width: 100% !important;">' +
            '<svg style="width: 42px; height: 42px; min-width: 42px; animation: dt-spin 0.75s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
            '<circle cx="12" cy="12" r="10" stroke="#fecdd3" stroke-width="3.5" fill="none"></circle>' +
            '<path fill="#e11d48" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
            '</svg>' +
            '</div>';

        window.dtSpinnerHtml = dtSpinnerHtml;

        if (typeof jQuery !== 'undefined' && jQuery.fn && jQuery.fn.dataTable) {
            jQuery.extend(true, jQuery.fn.dataTable.defaults, {
                processing: true,
                language: {
                    processing: dtSpinnerHtml
                }
            });
        }

        function getProcessingEl(settings) {
            if (!settings) return null;
            var $proc = jQuery('#' + settings.sTableId + '_processing');
            if (!$proc.length && settings.nTableWrapper) {
                $proc = jQuery(settings.nTableWrapper).find('.dt-processing, .dataTables_processing');
            }
            if (!$proc.length && settings.nTable) {
                var $wrapper = jQuery(settings.nTable).closest('.dt-container, .dataTables_wrapper, div[id$="_wrapper"]');
                $proc = $wrapper.find('.dt-processing, .dataTables_processing');
                if (!$proc.length && $wrapper.length) {
                    $proc = jQuery('<div id="' + settings.sTableId + '_processing" class="dt-processing" role="status"></div>');
                    $wrapper.prepend($proc);
                }
            }
            return $proc;
        }

        window.showDtLoading = function (settings) {
            if (!settings || typeof jQuery === 'undefined') return;
            var $table = jQuery(settings.nTable);
            var $proc = getProcessingEl(settings);
            $table.addClass('dt-table-loading');
            if ($proc && $proc.length) {
                $proc.addClass('is-loading').html(dtSpinnerHtml).attr('style', 'display: flex !important; position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; z-index: 9999 !important; margin: 0 !important; padding: 0 !important; background: transparent !important; border: none !important; box-shadow: none !important; width: auto !important; height: auto !important; pointer-events: none !important;');
            }
        };

        window.hideDtLoading = function (settings) {
            if (!settings || typeof jQuery === 'undefined') return;
            var $table = jQuery(settings.nTable);
            var $proc = getProcessingEl(settings);
            $table.removeClass('dt-table-loading');
            if ($proc && $proc.length) {
                $proc.removeClass('is-loading').attr('style', 'display: none !important;').hide();
            }
        };

        window.bindDtTable = function (settings) {
            if (!settings || !settings.nTable || typeof jQuery === 'undefined') return;
            jQuery(settings.nTable).off('processing.dt.logisDirect').on('processing.dt.logisDirect', function (ev, s, processing) {
                if (processing) {
                    window.showDtLoading(settings);
                } else {
                    window.hideDtLoading(settings);
                }
            });
        };

        if (typeof jQuery !== 'undefined') {
            jQuery(document).off('preXhr.dt.logisSpinner').on('preXhr.dt.logisSpinner', function (e, settings) {
                window.showDtLoading(settings);
            });

            jQuery(document).off('draw.dt.logisSpinner').on('draw.dt.logisSpinner', function (e, settings) {
                window.hideDtLoading(settings);
            });

            jQuery(document).off('xhr.dt.logisSpinner').on('xhr.dt.logisSpinner', function (e, settings) {
                window.hideDtLoading(settings);
            });

            jQuery(document).off('init.dt.logisSpinner').on('init.dt.logisSpinner', function (e, settings) {
                window.bindDtTable(settings);
            });
        }
    }

    initDataTablesLoading();

    // 7. Auto-scroll & Auto-focus to Active Sidebar Menu Item
    function scrollActiveSidebarMenu(options) {
        var smooth = options && options.smooth !== undefined ? options.smooth : true;

        var sidebar = document.getElementById('logis-sidebar') || document.getElementById('sidebar');
        if (!sidebar) return;

        var navContainer = document.getElementById('logis-sidebar-nav')
            || sidebar.querySelector('.overflow-y-auto')
            || document.getElementById('sidebar_menu_wrapper')
            || sidebar.querySelector('[data-kt-scrollable="true"]');
        if (!navContainer) return;

        // Priority 1: explicitly marked .active, [data-sidebar-active="true"], .kt-menu-item.active
        var activeElements = navContainer.querySelectorAll('.active, [data-sidebar-active="true"], .kt-menu-item.active, [data-kt-menu-item-here="true"]');
        var activeItem = null;

        if (activeElements.length > 0) {
            var activeLinks = Array.from(activeElements).filter(function (el) { return el.tagName === 'A'; });
            if (activeLinks.length > 0) {
                activeItem = activeLinks[activeLinks.length - 1];
            } else {
                activeItem = activeElements[activeElements.length - 1];
            }
        }

        // Priority 2: Fallback to color/background highlight classes if .active class was not rendered
        if (!activeItem) {
            var highlighted = navContainer.querySelectorAll('a[class*="bg-primary/"], a[class*="bg-[#1768B5]"], a[class*="bg-rose-50"], a[class*="bg-gray-100"]');
            if (highlighted.length > 0) {
                activeItem = highlighted[highlighted.length - 1];
            }
        }

        // Priority 3: Fallback by comparing href with window.location
        if (!activeItem) {
            var currentPath = window.location.pathname.replace(/\/+$/, '');
            var links = navContainer.querySelectorAll('a[href]');
            var bestMatch = null;
            var longestMatchLen = 0;

            links.forEach(function (link) {
                var href = link.getAttribute('href');
                if (!href || href === '#' || href.startsWith('javascript:')) return;
                try {
                    var url = new URL(link.href, window.location.origin);
                    var linkPath = url.pathname.replace(/\/+$/, '');
                    if (linkPath && currentPath === linkPath) {
                        bestMatch = link;
                        longestMatchLen = linkPath.length;
                    } else if (linkPath && linkPath !== '' && linkPath !== '/' && currentPath.startsWith(linkPath) && linkPath.length > longestMatchLen) {
                        bestMatch = link;
                        longestMatchLen = linkPath.length;
                    }
                } catch (e) {}
            });
            if (bestMatch) {
                activeItem = bestMatch;
            }
        }

        if (!activeItem) return;

        if (!activeItem.classList.contains('active')) {
            activeItem.classList.add('active');
        }

        // Calculate scroll offset to comfortably center the active item inside navContainer
        var containerRect = navContainer.getBoundingClientRect();
        var itemRect = activeItem.getBoundingClientRect();

        var relativeTop = (itemRect.top - containerRect.top) + navContainer.scrollTop;
        var targetScrollTop = relativeTop - (navContainer.clientHeight / 2) + (activeItem.clientHeight / 2);
        var maxScrollTop = navContainer.scrollHeight - navContainer.clientHeight;
        var clampedScrollTop = Math.max(0, Math.min(targetScrollTop, maxScrollTop));

        var isOutOfView = (
            itemRect.top < containerRect.top + 20 ||
            itemRect.bottom > containerRect.bottom - 20
        );

        if (isOutOfView || Math.abs(navContainer.scrollTop - clampedScrollTop) > 20) {
            if (typeof navContainer.scrollTo === 'function') {
                navContainer.scrollTo({
                    top: clampedScrollTop,
                    behavior: smooth ? 'smooth' : 'auto'
                });
            } else {
                navContainer.scrollTop = clampedScrollTop;
            }
        }
    }

    window.scrollActiveSidebarMenu = scrollActiveSidebarMenu;

    // Run auto-scroll on page initialization
    setTimeout(function () {
        scrollActiveSidebarMenu({ smooth: false });
    }, 60);

    setTimeout(function () {
        scrollActiveSidebarMenu({ smooth: true });
    }, 280);
});

