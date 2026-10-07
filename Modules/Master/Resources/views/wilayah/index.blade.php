@extends('layouts.app')

@section('title', 'Master Wilayah - ' . config('app.name'))
@section('page_title', 'Master Wilayah')

@section('breadcrumb')
    <a class="text-secondary-foreground hover:text-primary" href="{{ url('/') }}">Home</a>
    <span class="text-muted-foreground text-sm">/</span>
    <span class="text-secondary-foreground">Master</span>
    <span class="text-muted-foreground text-sm">/</span>
    <span class="text-mono">Wilayah</span>
@endsection

@push('styles')
<style>
    .tree-node { border-left: 1px solid var(--border); margin-left: 12px; }
    .tree-node-root { border-left: none; margin-left: 0; }
    .tree-item { display: flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 6px; cursor: default; transition: background 0.15s; font-size: 0.875rem; }
    .tree-item:hover { background: var(--muted); }
    .tree-toggle { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 4px; transition: background 0.15s; flex-shrink: 0; }
    .tree-toggle:hover { background: var(--border); }
    .tree-toggle i { font-size: 10px; transition: transform 0.2s; color: var(--secondary-foreground); }
    .tree-toggle.expanded i { transform: rotate(90deg); }
    .tree-placeholder { width: 20px; flex-shrink: 0; }
    .tree-icon { color: var(--primary); flex-shrink: 0; }
    .tree-label { flex: 1; font-weight: 500; color: var(--foreground); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .tree-badge { font-size: 0.7rem; padding: 1px 6px; border-radius: 10px; background: var(--muted); color: var(--secondary-foreground); font-weight: 500; flex-shrink: 0; }
    .tree-actions { display: flex; gap: 2px; opacity: 0; transition: opacity 0.15s; flex-shrink: 0; }
    .tree-item:hover .tree-actions { opacity: 1; }
    .tree-actions button { padding: 3px 6px; border-radius: 4px; border: none; background: transparent; cursor: pointer; font-size: 0.75rem; transition: background 0.15s; }
    .tree-actions .btn-add { color: var(--primary); }
    .tree-actions .btn-add:hover { background: color-mix(in oklab, var(--primary) 10%, transparent); }
    .tree-actions .btn-edit { color: #f59e0b; }
    .tree-actions .btn-edit:hover { background: color-mix(in oklab, #f59e0b 10%, transparent); }
    .tree-actions .btn-delete { color: #ef4444; }
    .tree-actions .btn-delete:hover { background: color-mix(in oklab, #ef4444 10%, transparent); }
    .tree-children { overflow: hidden; transition: max-height 0.25s ease; }
    .tree-children.collapsed { max-height: 0 !important; }
    .tree-loading { padding: 6px 10px 6px 36px; font-size: 0.8rem; color: var(--secondary-foreground); display: flex; align-items: center; gap: 6px; }
    .tree-spinner { width: 14px; height: 14px; border: 2px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: tree-spin 0.6s linear infinite; display: inline-block; }
    @keyframes tree-spin { to { transform: rotate(360deg); } }
    .tree-toggle.loading i { display: none; }
    .tree-toggle.loading::after { content: ''; width: 10px; height: 10px; border: 1.5px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: tree-spin 0.6s linear infinite; }
    .tree-empty { padding: 6px 10px 6px 36px; font-size: 0.8rem; color: var(--muted-foreground); font-style: italic; }
    .level-provinsi > .tree-item .tree-icon { color: var(--primary); }
    .level-kota_kab > .tree-item .tree-icon { color: #f59e0b; }
    .level-kecamatan > .tree-item .tree-icon { color: #10b981; }
    .level-desa > .tree-item .tree-icon { color: #8b5cf6; }
</style>
@endpush

@section('content')
<div class="kt-container-fixed">
    @if(Session::has('success'))
        <div class="kt-alert kt-alert-success mb-5">
            <div class="kt-alert-content">
                <p class="kt-alert-description">{{ Session::get('success') }}</p>
            </div>
        </div>
    @endif

    <div class="kt-card min-w-full">
        <div class="kt-card-header flex-wrap gap-2">
            <h3 class="kt-card-title text-sm">Data Wilayah Indonesia</h3>
            <div class="flex flex-wrap gap-2 lg:gap-5">
                <div class="flex">
                    <label class="kt-input">
                        <i class="ki-filled ki-magnifier"></i>
                        <input id="tree-search" placeholder="Cari wilayah..." type="text"/>
                    </label>
                </div>
                <button class="kt-btn kt-btn-sm kt-btn-primary" id="btn-add-provinsi">
                    <i class="ki-filled ki-plus"></i> Tambah Provinsi
                </button>
            </div>
        </div>
        <div class="kt-card-content">
            <div id="tree-container" class="p-4 tree-node tree-node-root"></div>
        </div>
    </div>
</div>

<div class="kt-modal" id="modal-form" data-kt-modal="true">
    <div class="kt-modal-content kt-modal-content-solid w-[450px]">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title" id="modal-title">Tambah Wilayah</h3>
            <button class="kt-modal-close" data-kt-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="kt-modal-body">
            <form id="form-wilayah">
                @csrf
                <input type="hidden" id="form-level" name="level">
                <input type="hidden" id="form-parent-id" name="parent_id">
                <input type="hidden" id="form-method" value="POST">
                <div class="flex flex-col gap-4">
                    <div>
                        <label class="kt-label">Kode</label>
                        <input type="text" id="form-id" name="id" class="kt-input w-full" placeholder="Kode wilayah" required>
                    </div>
                    <div>
                        <label class="kt-label">Nama</label>
                        <input type="text" id="form-name" name="name" class="kt-input w-full" placeholder="Nama wilayah" required>
                    </div>
                </div>
            </form>
        </div>
        <div class="kt-modal-footer">
            <button class="kt-btn kt-btn-outline" data-kt-modal-dismiss="true">Batal</button>
            <button class="kt-btn kt-btn-primary" id="btn-submit-form">Simpan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var TOKEN = '{{ csrf_token() }}';
    var CHILDREN_URL = '{{ route("wilayah.get-children") }}';
    var STORE_URL = '{{ route("wilayah.store") }}';
    var UPDATE_URL_TEMPLATE = '{{ route("wilayah.update", ":id") }}';
    var DESTROY_URL_TEMPLATE = '{{ route("wilayah.destroy", ":id") }}';

    var LEVEL_LABELS = { provinsi: 'Provinsi', kota_kab: 'Kota/Kabupaten', kecamatan: 'Kecamatan', desa: 'Desa' };
    var LEVEL_ICONS = { provinsi: 'ki-office-building', kota_kab: 'ki-map', kecamatan: 'ki-pin', desa: 'ki-home-2' };
    var CHILD_LEVELS = { provinsi: 'kota_kab', kota_kab: 'kecamatan', kecamatan: 'desa', desa: null };

    var loadedNodes = {};

    function initTree() {
        loadChildren('tree-container', 'provinsi', null, null);
    }

    function loadChildren(containerId, level, parentId, toggleEl) {
        var container = document.getElementById(containerId);
        if (!container) return;
        if (toggleEl) toggleEl.classList.add('loading');
        container.innerHTML = '<div class="tree-loading"><span class="tree-spinner"></span> Memuat data...</div>';
        container.classList.remove('collapsed');
        container.style.maxHeight = '60px';

        var url = CHILDREN_URL + '?level=' + encodeURIComponent(level);
        if (parentId) url += '&parent_id=' + encodeURIComponent(parentId);

        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            if (toggleEl) toggleEl.classList.remove('loading');
            if (xhr.status !== 200) {
                container.innerHTML = '<div class="tree-empty" style="color:#ef4444">Gagal memuat data (HTTP ' + xhr.status + ')</div>';
                container.style.maxHeight = '40px';
                return;
            }
            var res;
            try { res = JSON.parse(xhr.responseText); } catch(e) {
                container.innerHTML = '<div class="tree-empty" style="color:#ef4444">Response tidak valid</div>';
                container.style.maxHeight = '40px';
                return;
            }
            container.innerHTML = '';
            if (!res.items || res.items.length === 0) {
                container.innerHTML = '<div class="tree-empty">Tidak ada data</div>';
                container.style.maxHeight = '40px';
                return;
            }
            res.items.forEach(function(item) {
                container.insertAdjacentHTML('beforeend', buildNodeHtml(item, level));
            });
            loadedNodes[containerId] = true;
            container.style.maxHeight = container.scrollHeight + 'px';
            filterTree();
        };
        xhr.onerror = function() {
            if (toggleEl) toggleEl.classList.remove('loading');
            container.innerHTML = '<div class="tree-empty" style="color:#ef4444">Gagal memuat data</div>';
            container.style.maxHeight = '40px';
        };
        xhr.send();
    }

    function toggleNode(toggleEl) {
        var targetId = toggleEl.getAttribute('data-target');
        var children = document.getElementById(targetId);
        if (!children) return;

        if (children.classList.contains('collapsed')) {
            toggleEl.classList.add('expanded');
            if (!loadedNodes[targetId]) {
                var raw = targetId.replace('children-', '');
                var dashIndex = raw.indexOf('-');
                var parentLevel = raw.substring(0, dashIndex);
                var parentId = raw.substring(dashIndex + 1);
                var childLevel = CHILD_LEVELS[parentLevel];
                loadChildren(targetId, childLevel, parentId, toggleEl);
            } else {
                children.classList.remove('collapsed');
                children.style.maxHeight = children.scrollHeight + 'px';
            }
        } else {
            children.classList.add('collapsed');
            toggleEl.classList.remove('expanded');
            setTimeout(function() { children.style.maxHeight = '0'; }, 250);
        }
    }

    function buildNodeHtml(item, level) {
        var childLevel = CHILD_LEVELS[level];
        var nodeId = 'node-' + level + '-' + item.id;
        var childContainerId = 'children-' + level + '-' + item.id;
        var icon = LEVEL_ICONS[level];
        var isLeaf = !item.has_children;
        var safeName = item.name.replace(/'/g, "\\'").replace(/"/g, '&quot;');

        var toggleHtml = isLeaf
            ? '<span class="tree-placeholder"></span>'
            : '<span class="tree-toggle" onclick="window._wilayahToggle(this)" data-target="' + childContainerId + '"><i class="ki-filled ki-right"></i></span>';

        var actions = '<span class="tree-actions">';
        if (childLevel) {
            actions += '<button class="btn-add" onclick="window._wilayahAdd(\'' + childLevel + '\',\'' + item.id + '\')" title="Tambah ' + LEVEL_LABELS[childLevel] + '"><i class="ki-filled ki-plus"></i></button>';
        }
        actions += '<button class="btn-edit" onclick="window._wilayahEdit(\'' + level + '\',\'' + item.id + '\',\'' + safeName + '\')" title="Edit"><i class="ki-filled ki-notepad-edit"></i></button>';
        actions += '<button class="btn-delete" onclick="window._wilayahDelete(\'' + level + '\',\'' + item.id + '\')" title="Hapus"><i class="ki-filled ki-trash"></i></button>';
        actions += '</span>';

        var badge = item.count > 0 ? '<span class="tree-badge">' + item.count + '</span>' : '';
        var childrenDiv = isLeaf ? '' : '<div class="tree-children collapsed" id="' + childContainerId + '"></div>';

        return '<div class="tree-node level-' + level + '" id="' + nodeId + '" data-name="' + item.name.toLowerCase() + '">'
            + '<div class="tree-item">' + toggleHtml
            + '<span class="tree-icon"><i class="ki-filled ' + icon + '"></i></span>'
            + '<span class="tree-label">' + item.name + '</span>'
            + badge + actions
            + '</div>' + childrenDiv
            + '</div>';
    }

    function postAction(url, data, onSuccess) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-CSRF-TOKEN', TOKEN);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            var res;
            try { res = JSON.parse(xhr.responseText); } catch(e) { res = { status: 'error', message: 'Response tidak valid' }; }
            onSuccess(res);
        };
        xhr.onerror = function() { onSuccess({ status: 'error', message: 'Network error' }); };
        var params = [];
        for (var k in data) {
            if (data[k] !== null && data[k] !== undefined) params.push(encodeURIComponent(k) + '=' + encodeURIComponent(data[k]));
        }
        xhr.send(params.join('&'));
    }

    window._wilayahToggle = function(el) { toggleNode(el); };

    window._wilayahAdd = function(level, parentId) {
        document.getElementById('modal-title').textContent = 'Tambah ' + LEVEL_LABELS[level];
        document.getElementById('form-level').value = level;
        document.getElementById('form-parent-id').value = parentId || '';
        document.getElementById('form-method').value = 'POST';
        var fId = document.getElementById('form-id');
        fId.value = '';
        fId.readOnly = false;
        fId.style.display = '';
        document.getElementById('form-name').value = '';
        KTModal.show(document.getElementById('modal-form'));
    };

    window._wilayahEdit = function(level, id, name) {
        document.getElementById('modal-title').textContent = 'Edit ' + LEVEL_LABELS[level];
        document.getElementById('form-level').value = level;
        document.getElementById('form-parent-id').value = '';
        document.getElementById('form-method').value = 'PUT';
        var fId = document.getElementById('form-id');
        fId.value = id;
        fId.readOnly = true;
        fId.style.display = '';
        document.getElementById('form-name').value = name;
        KTModal.show(document.getElementById('modal-form'));
    };

    window._wilayahDelete = function(level, id) {
        Swal.fire({
            title: 'Yakin hapus?',
            text: 'Data ' + LEVEL_LABELS[level] + ' akan dihapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'kt-btn kt-btn-destructive me-2', cancelButton: 'kt-btn kt-btn-outline' },
            buttonsStyling: false
        }).then(function(result) {
            if (result.isConfirmed) {
                var url = DESTROY_URL_TEMPLATE.replace(':id', id);
                postAction(url, { _method: 'DELETE', level: level }, function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ title: 'Terhapus!', text: res.message, icon: 'success', timer: 1200, showConfirmButton: false });
                        loadedNodes = {};
                        initTree();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                });
            }
        });
    };

    document.getElementById('btn-add-provinsi').addEventListener('click', function() {
        window._wilayahAdd('provinsi', null);
    });

    document.getElementById('btn-submit-form').addEventListener('click', function() {
        var level = document.getElementById('form-level').value;
        var method = document.getElementById('form-method').value;
        var id = document.getElementById('form-id').value;
        var name = document.getElementById('form-name').value;
        var parentId = document.getElementById('form-parent-id').value;

        if (!name.trim()) { Swal.fire('Error', 'Nama wajib diisi', 'error'); return; }
        if (method === 'POST' && !id.trim()) { Swal.fire('Error', 'Kode wajib diisi', 'error'); return; }

        var url = method === 'PUT' ? UPDATE_URL_TEMPLATE.replace(':id', id) : STORE_URL;
        var data = { level: level, id: id, name: name, parent_id: parentId, _method: method };

        postAction(url, data, function(res) {
            if (res.status === 'success') {
                Swal.fire({ title: 'Berhasil!', text: res.message, icon: 'success', timer: 1200, showConfirmButton: false });
                KTModal.hide(document.getElementById('modal-form'));
                loadedNodes = {};
                initTree();
            } else {
                Swal.fire('Error', res.message || 'Gagal', 'error');
            }
        });
    });

    document.getElementById('tree-search').addEventListener('keyup', function() { filterTree(); });

    function filterTree() {
        var query = document.getElementById('tree-search').value.toLowerCase().trim();
        var nodes = document.querySelectorAll('#tree-container .tree-node');
        if (!query) {
            nodes.forEach(function(n) { n.style.display = ''; });
            return;
        }
        nodes.forEach(function(n) {
            var name = n.getAttribute('data-name') || '';
            if (name.indexOf(query) !== -1) {
                n.style.display = '';
                var parent = n.parentElement;
                while (parent && parent.id !== 'tree-container') {
                    if (parent.classList && parent.classList.contains('tree-node')) parent.style.display = '';
                    parent = parent.parentElement;
                }
            } else {
                n.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() { initTree(); });
})();
</script>
@endpush
