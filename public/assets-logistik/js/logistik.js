/**
 * LogisPro Theme Interactive Utilities & UI Handlers
 */
document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize Lucide Icons
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
    }

    // 2. Mobile Sidebar Toggle
    const sidebarToggleBtn = document.getElementById('logis-sidebar-toggle');
    const sidebarCloseBtn = document.getElementById('logis-sidebar-close');
    const sidebar = document.getElementById('logis-sidebar');
    const sidebarBackdrop = document.getElementById('logis-sidebar-backdrop');

    function openSidebar() {
        if (sidebar) sidebar.classList.remove('-translate-x-full');
        if (sidebarBackdrop) sidebarBackdrop.classList.remove('hidden');
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.add('-translate-x-full');
        if (sidebarBackdrop) sidebarBackdrop.classList.add('hidden');
    }

    if (sidebarToggleBtn) sidebarToggleBtn.addEventListener('click', openSidebar);
    if (sidebarCloseBtn) sidebarCloseBtn.addEventListener('click', closeSidebar);
    if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);

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
});

