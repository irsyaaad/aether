@extends('layouts.app')

@section('title', 'Team Crew - ' . config('app.name'))

@push('styles')
<style>
#team_crew_table_wrapper .dt-layout-row { align-items: center; margin-bottom: 1rem; }
#team_crew_table_wrapper .dt-length label { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; }
#team_crew_table_wrapper .dt-search label { display: flex; align-items: center; gap: 0.5rem; }
#team_crew_table_wrapper .dt-search input { border: 1px solid var(--border, #e2e5ec); border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; background: transparent; }
#team_crew_table_wrapper .dt-info { font-size: 0.875rem; color: var(--secondary-foreground, #78829d); }
#team_crew_table_wrapper .dt-paging nav { display: flex; gap: 0.25rem; }
#team_crew_table_wrapper .dt-paging button { padding: 0.375rem 0.75rem; border: 1px solid var(--border, #e2e5ec); border-radius: 0.375rem; background: transparent; font-size: 0.875rem; cursor: pointer; color: var(--foreground); }
#team_crew_table_wrapper .dt-paging button.current { background: var(--primary, #3e97ff); color: #fff; border-color: var(--primary, #3e97ff); }
#team_crew_table_wrapper .dt-paging button:hover:not(.current) { background: var(--accent); }
#team_crew_table_wrapper select { border: 1px solid var(--border, #e2e5ec); border-radius: 0.375rem; padding: 0.25rem 0.5rem; font-size: 0.875rem; background: transparent; color: var(--foreground); }
#team_crew_table_wrapper { width: 100% !important; }
#team_crew_table_wrapper .dt-layout-row { width: 100% !important; }
#team_crew_table_wrapper .dt-layout-cell { width: 100% !important; padding: 0 !important; }
#team_crew_table_wrapper .dt-layout-table { width: 100% !important; }
#team_crew_table_wrapper table { width: 100% !important; table-layout: auto !important; }
.dataTables_empty { padding: 2rem !important; text-align: center; color: var(--secondary-foreground); }
</style>
@endpush

@section('content')
<div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
    <div class="flex flex-col justify-center gap-2">
        <h1 class="text-xl font-medium leading-none text-mono">Team Crew</h1>
        <div class="flex items-center flex-wrap gap-1.5 font-medium">
            <span class="text-base text-secondary-foreground">All Members:</span>
            <span class="text-base text-foreground font-medium me-2" id="total_members">{{ $total }}</span>
            <span class="text-base text-secondary-foreground">Pro Licenses</span>
            <span class="text-base text-foreground font-medium" id="pro_licenses">{{ max(1, round($total * 0.015)) }}</span>
        </div>
    </div>
    <div class="flex items-center gap-2.5">
        <a class="kt-btn kt-btn-outline" href="javascript:void(0)" onclick="alert('CSV import coming soon')">
            Import CSV
        </a>
        <button class="kt-btn kt-btn-primary" data-kt-modal-toggle="#add_member_modal">
            Add Member
        </button>
    </div>
</div>

<div class="grid gap-5 lg:gap-7.5">
    <div class="kt-card kt-card-grid min-w-full">
        <div class="kt-card-content">
            <table class="kt-table table-auto kt-table-border w-full" id="team_crew_table" style="width:100%">
                <thead>
                    <tr>
                        <th class="min-w-[300px]">
                            <span class="kt-table-col">
                                <span class="kt-table-col-label">Member</span>
                                <span class="kt-table-col-sort"></span>
                            </span>
                        </th>
                        <th class="min-w-[180px]">
                            <span class="kt-table-col">
                                <span class="kt-table-col-label">Role</span>
                                <span class="kt-table-col-sort"></span>
                            </span>
                        </th>
                        <th class="min-w-[180px]">
                            <span class="kt-table-col">
                                <span class="kt-table-col-label">Status</span>
                                <span class="kt-table-col-sort"></span>
                            </span>
                        </th>
                        <th class="min-w-[180px]">
                            <span class="kt-table-col">
                                <span class="kt-table-col-label">Location</span>
                                <span class="kt-table-col-sort"></span>
                            </span>
                        </th>
                        <th class="min-w-[180px]">
                            <span class="kt-table-col">
                                <span class="kt-table-col-label">Activity</span>
                                <span class="kt-table-col-sort"></span>
                            </span>
                        </th>
                        <th class="w-[60px]"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- Add Member Modal -->
<div class="kt-modal" data-kt-modal="true" id="add_member_modal">
    <div class="kt-modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title">Add Member</h3>
            <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost shrink-0" data-kt-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <form id="add_member_form" class="flex flex-col">
            @csrf
            <div class="kt-modal-body grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5 px-5">
                    <label class="text-sm font-medium text-mono">Full Name</label>
                    <label class="kt-input">
                        <input type="text" name="name" required placeholder="Enter full name"/>
                    </label>
                    <span class="text-xs text-destructive hidden error-name"></span>
                </div>
                <div class="flex flex-col gap-2.5 px-5">
                    <label class="text-sm font-medium text-mono">Email</label>
                    <label class="kt-input">
                        <input type="email" name="email" required placeholder="Enter email address"/>
                    </label>
                    <span class="text-xs text-destructive hidden error-email"></span>
                </div>
                <div class="flex flex-col gap-2.5 px-5">
                    <label class="text-sm font-medium text-mono">Password</label>
                    <label class="kt-input">
                        <input type="password" name="password" required placeholder="Min. 8 characters"/>
                    </label>
                    <span class="text-xs text-destructive hidden error-password"></span>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2.5 px-5 py-5 border-t border-t-border">
                <button class="kt-btn kt-btn-outline" type="button" data-kt-modal-dismiss="true">Cancel</button>
                <button class="kt-btn kt-btn-primary" type="submit" id="btn_add_submit">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Member Modal -->
<div class="kt-modal" data-kt-modal="true" id="edit_member_modal">
    <div class="kt-modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title">Edit Member</h3>
            <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost shrink-0" data-kt-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <form id="edit_member_form" class="flex flex-col">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" id="edit_user_id"/>
            <div class="kt-modal-body grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5 px-5">
                    <label class="text-sm font-medium text-mono">Full Name</label>
                    <label class="kt-input">
                        <input type="text" name="name" id="edit_name" required placeholder="Enter full name"/>
                    </label>
                    <span class="text-xs text-destructive hidden error-name"></span>
                </div>
                <div class="flex flex-col gap-2.5 px-5">
                    <label class="text-sm font-medium text-mono">Email</label>
                    <label class="kt-input">
                        <input type="email" name="email" id="edit_email" required placeholder="Enter email address"/>
                    </label>
                    <span class="text-xs text-destructive hidden error-email"></span>
                </div>
                <div class="flex flex-col gap-2.5 px-5">
                    <label class="text-sm font-medium text-mono">Password <span class="text-muted-foreground font-normal">(leave empty to keep current)</span></label>
                    <label class="kt-input">
                        <input type="password" name="password" id="edit_password" placeholder="Min. 8 characters"/>
                    </label>
                    <span class="text-xs text-destructive hidden error-password"></span>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2.5 px-5 py-5 border-t border-t-border">
                <button class="kt-btn kt-btn-outline" type="button" data-kt-modal-dismiss="true">Cancel</button>
                <button class="kt-btn kt-btn-primary" type="submit" id="btn_edit_submit">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- View Member Modal -->
<div class="kt-modal" data-kt-modal="true" id="view_member_modal">
    <div class="kt-modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title">Member Details</h3>
            <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost shrink-0" data-kt-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="kt-modal-body px-7.5 py-7.5">
            <div class="flex flex-col items-center gap-4">
                <img alt="" class="rounded-full size-20 shrink-0 border-2 border-border" id="view_avatar" src=""/>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-mono" id="view_name"></h3>
                    <a class="text-sm text-secondary-foreground hover:text-primary" id="view_email" href=""></a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-6 pt-5 border-t border-t-border">
                <div>
                    <span class="text-xs text-muted-foreground font-medium uppercase">Role</span>
                    <p class="text-sm font-medium text-foreground mt-1" id="view_role"></p>
                </div>
                <div>
                    <span class="text-xs text-muted-foreground font-medium uppercase">Status</span>
                    <p class="text-sm font-medium text-foreground mt-1" id="view_status"></p>
                </div>
                <div>
                    <span class="text-xs text-muted-foreground font-medium uppercase">Location</span>
                    <p class="text-sm font-medium text-foreground mt-1" id="view_location"></p>
                </div>
                <div>
                    <span class="text-xs text-muted-foreground font-medium uppercase">Activity</span>
                    <p class="text-sm font-medium text-foreground mt-1" id="view_activity"></p>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end px-7.5 py-5 border-t border-t-border">
            <button class="kt-btn kt-btn-outline" type="button" data-kt-modal-dismiss="true">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#team_crew_table').DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: '{{ route('users.data') }}',
        pageLength: 10,
        order: [[0, 'asc']],
        language: {
            search: '',
            searchPlaceholder: 'Search users...',
            info: 'Showing _START_ to _END_ of _TOTAL_ users',
            infoEmpty: 'Showing 0 users',
            infoFiltered: '(filtered from _MAX_ total users)',
            lengthMenu: 'Show _MENU_ per page',
            loadingRecords: '&nbsp;',
            paginate: {
                first: '&laquo;',
                previous: '&lsaquo;',
                next: '&rsaquo;',
                last: '&raquo;'
            }
        },
        layout: {
            topStart: ['pageLength'],
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        columnDefs: [
            { orderable: false, targets: [5] },
        ],
        columns: [
            {
                data: null,
                render: function(data) {
                    return '<div class="flex items-center gap-2.5">' +
                        '<img alt="" class="rounded-full size-9 shrink-0" src="' + data.avatar + '"/>' +
                        '<div class="flex flex-col">' +
                        '<a class="text-sm font-medium text-mono hover:text-primary mb-px" href="javascript:void(0)" onclick="viewMember(' + data.id + ')">' + data.name + '</a>' +
                        '<a class="text-sm text-secondary-foreground font-normal hover:text-primary" href="mailto:' + data.email + '">' + data.email + '</a>' +
                        '</div>' +
                        '</div>';
                }
            },
            {
                data: 'role',
                render: function(data) {
                    return '<span class="text-foreground font-normal">' + data + '</span>';
                }
            },
            {
                data: null,
                render: function(data) {
                    return '<span class="kt-badge ' + data.status_class + ' kt-badge-outline rounded-[30px]">' +
                        '<span class="kt-badge-dot size-1.5"></span> ' + data.status_label +
                        '</span>';
                }
            },
            {
                data: null,
                render: function(data) {
                    return '<div class="flex items-center text-foreground font-normal gap-1.5">' +
                        '<img alt="" class="rounded-full size-4 shrink-0" src="assets/media/flags/' + data.location_flag + '"/> ' + data.location +
                        '</div>';
                }
            },
            { data: 'activity' },
            {
                data: null,
                render: function(data) {
                    return '<div class="kt-menu flex-inline" data-kt-menu="true">' +
                        '<div class="kt-menu-item" data-kt-menu-item-offset="0, 10px" data-kt-menu-item-placement="bottom-end" data-kt-menu-item-placement-rtl="bottom-start" data-kt-menu-item-toggle="dropdown" data-kt-menu-item-trigger="click">' +
                        '<button class="kt-menu-toggle kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost">' +
                        '<i class="ki-filled ki-dots-vertical text-lg"></i>' +
                        '</button>' +
                        '<div class="kt-menu-dropdown kt-menu-default w-full max-w-[175px]" data-kt-menu-dismiss="true">' +
                        '<div class="kt-menu-item">' +
                        '<a class="kt-menu-link" href="javascript:void(0)" onclick="viewMember(' + data.id + ')">' +
                        '<span class="kt-menu-icon"><i class="ki-filled ki-search-list"></i></span>' +
                        '<span class="kt-menu-title">View</span>' +
                        '</a>' +
                        '</div>' +
                        '<div class="kt-menu-item">' +
                        '<a class="kt-menu-link" href="javascript:void(0)" onclick="alert(\'Export feature coming soon\')">' +
                        '<span class="kt-menu-icon"><i class="ki-filled ki-file-up"></i></span>' +
                        '<span class="kt-menu-title">Export</span>' +
                        '</a>' +
                        '</div>' +
                        '<div class="kt-menu-separator"></div>' +
                        '<div class="kt-menu-item">' +
                        '<a class="kt-menu-link" href="javascript:void(0)" onclick="editMember(' + data.id + ')">' +
                        '<span class="kt-menu-icon"><i class="ki-filled ki-pencil"></i></span>' +
                        '<span class="kt-menu-title">Edit</span>' +
                        '</a>' +
                        '</div>' +
                        '<div class="kt-menu-item">' +
                        '<a class="kt-menu-link" href="javascript:void(0)" onclick="alert(\'Copy feature coming soon\')">' +
                        '<span class="kt-menu-icon"><i class="ki-filled ki-copy"></i></span>' +
                        '<span class="kt-menu-title">Make a copy</span>' +
                        '</a>' +
                        '</div>' +
                        '<div class="kt-menu-separator"></div>' +
                        '<div class="kt-menu-item">' +
                        '<a class="kt-menu-link" href="javascript:void(0)" onclick="deleteMember(' + data.id + ', \'' + data.name.replace(/'/g, "\\'") + '\')">' +
                        '<span class="kt-menu-icon"><i class="ki-filled ki-trash"></i></span>' +
                        '<span class="kt-menu-title">Remove</span>' +
                        '</a>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                }
            }
        ],
        drawCallback: function() {
            var info = table.page.info();
            $('#total_members').text(info.recordsTotal.toLocaleString());
            $('#pro_licenses').text(Math.round(info.recordsTotal * 0.015).toLocaleString());
        }
    });

    // Override search input styling
    setTimeout(function() {
        $('.dt-search input').addClass('kt-input');
        $('.dt-length select').addClass('kt-select');
    }, 100);

    // Delete (handled via SweetAlert in deleteMember function)

    // Add Member
    $('#add_member_form').on('submit', function(e) {
        e.preventDefault();
        $('#btn_add_submit').prop('disabled', true).text('Saving...');
        $('.error-name, .error-email, .error-password').addClass('hidden');
        $.ajax({
            url: '{{ route('users.store') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                $('#add_member_form')[0].reset();
                $('[data-kt-modal-dismiss]').filter(':visible').first().trigger('click');
                table.ajax.reload();
                $('#btn_add_submit').prop('disabled', false).text('Save');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Member has been added.',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                $('#btn_add_submit').prop('disabled', false).text('Save');
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) { $('.error-name').removeClass('hidden').text(errors.name[0]); }
                    if (errors.email) { $('.error-email').removeClass('hidden').text(errors.email[0]); }
                    if (errors.password) { $('.error-password').removeClass('hidden').text(errors.password[0]); }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        });
    });

    // Edit Member
    $('#edit_member_form').on('submit', function(e) {
        e.preventDefault();
        var id = $('#edit_user_id').val();
        $('#btn_edit_submit').prop('disabled', true).text('Updating...');
        $('.error-name, .error-email, .error-password').addClass('hidden');
        $.ajax({
            url: '{{ url('/users') }}/' + id,
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                $('[data-kt-modal-dismiss]').filter(':visible').first().trigger('click');
                table.ajax.reload();
                $('#btn_edit_submit').prop('disabled', false).text('Update');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Member has been updated.',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                $('#btn_edit_submit').prop('disabled', false).text('Update');
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) { $('.error-name').removeClass('hidden').text(errors.name[0]); }
                    if (errors.email) { $('.error-email').removeClass('hidden').text(errors.email[0]); }
                    if (errors.password) { $('.error-password').removeClass('hidden').text(errors.password[0]); }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        });
    });
});

function viewMember(id) {
    $.get('{{ url('/users') }}/' + id + '/json', function(data) {
        $('#view_avatar').attr('src', data.avatar);
        $('#view_name').text(data.name);
        $('#view_email').text(data.email).attr('href', 'mailto:' + data.email);
        $('#view_role').text(data.role);
        $('#view_status').html('<span class="kt-badge ' + data.status_class + ' kt-badge-outline rounded-[30px]"><span class="kt-badge-dot size-1.5"></span> ' + data.status_label + '</span>');
        $('#view_location').text(data.location);
        $('#view_activity').text(data.activity);
        var modal = KTModal.getOrCreateInstance(document.getElementById('view_member_modal'));
        modal.show();
    });
}

function editMember(id) {
    $.get('{{ url('/users') }}/' + id + '/json', function(data) {
        $('#edit_user_id').val(data.id);
        $('#edit_name').val(data.name);
        $('#edit_email').val(data.email);
        $('#edit_password').val('');
        $('.error-name, .error-email, .error-password').addClass('hidden');
        var modal = KTModal.getOrCreateInstance(document.getElementById('edit_member_modal'));
        modal.show();
    });
}

function deleteMember(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Remove ' + name + '? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#ef4444',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'kt-btn kt-btn-destructive',
            cancelButton: 'kt-btn kt-btn-outline',
            popup: 'kt-modal-content',
            header: 'kt-modal-header',
            title: 'kt-modal-title'
        }
    }).then(function(result) {
        if (!result.isConfirmed) return;
        var form = $('#delete-form');
        form.attr('action', '{{ url('/users') }}/' + id);
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: name + ' has been removed.',
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#team_crew_table').DataTable().ajax.reload();
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });
}
</script>
@endpush