<div class="hidden kt-drawer kt-drawer-end card flex-col max-w-[90%] w-[450px] top-5 bottom-5 end-5 rounded-xl border border-border"
     data-kt-drawer="true"
     data-kt-drawer-container="body"
     id="notifications_drawer">
    <div class="flex items-center justify-between gap-2.5 text-sm text-mono font-semibold px-5 py-2.5 border-b border-b-border" id="notifications_header">
        Notifications
        <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-drawer-dismiss="true">
            <i class="ki-filled ki-cross"></i>
        </button>
    </div>
    <div class="kt-tabs kt-tabs-line justify-between px-5 mb-2" data-kt-tabs="true" id="notifications_tabs">
        <div class="flex items-center gap-5">
            <button class="kt-tab-toggle py-3 active" data-kt-tab-toggle="#notifications_tab_all">All</button>
            <button class="kt-tab-toggle py-3 relative" data-kt-tab-toggle="#notifications_tab_inbox">
                Inbox
                <span class="rounded-full bg-green-500 size-[5px] absolute top-2 rtl:start-0 end-0 transform translate-y-1/2 translate-x-full"></span>
            </button>
            <button class="kt-tab-toggle py-3" data-kt-tab-toggle="#notifications_tab_team">Team</button>
            <button class="kt-tab-toggle py-3" data-kt-tab-toggle="#notifications_tab_following">Following</button>
        </div>
    </div>
    <div class="flex items-center justify-center grow text-sm text-secondary-foreground p-10">
        No new notifications
    </div>
</div>
