@extends('template.root')
@section('page-name', 'Rekrutment')
@section('title-page', 'Seleksi Kandidat')

@section('content')
    @php
        $canCreate = check_access('rekrutment.create');
        $canUpdate = check_access('rekrutment.update');
        $canDelete = check_access('rekrutment.delete');
    @endphp

    <div class="kanban-toolbar">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="GET" action="{{ route('rekrutment.index') }}" class="d-flex gap-2 align-items-center">
                <label class="mb-0 fw-semibold">Lowongan</label>
                <select name="ads_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 220px;">
                    <option value="all">Semua Lowongan</option>
                    @foreach($ads as $ad)
                        <option value="{{ $ad->id }}" {{ isset($selectedAdsId) && $selectedAdsId == $ad->id ? 'selected' : '' }}>
                            {{ $ad->title }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="kanban-toolbar-info text-muted">
            <small>Drag & drop kandidat antar kolom untuk mengubah status seleksi.</small>
        </div>
    </div>

    @forelse($boards as $board)
        <div class="mb-4">
            <h6 class="text-primary mb-3 pb-2 border-bottom">
                <i class="fa fa-bullhorn me-2"></i>
                {{ $board['ads']->title }}
                <small class="text-muted ms-2">({{ $board['ads']->remarks ?? 'Tidak ada tempat posting' }})</small>
            </h6>

            <div class="kanban-board" id="kanban-board">
                @foreach($board['columns'] as $key => $column)
                    <div class="kanban-column">
                        <div class="kanban-column-head">
                            <div>
                                <h6 class="mb-0">{{ $column['name'] }}</h6>
                                <small class="text-muted">{{ $column['kandidats']->count() }} kandidat</small>
                            </div>
                        </div>

                        <div class="kanban-cards" data-status="{{ $key }}">
                            @forelse($column['kandidats'] as $kandidat)
                                <div class="kanban-card"
                                     draggable="{{ $canUpdate ? 'true' : 'false' }}"
                                     data-id="{{ $kandidat->id }}"
                                     data-status="{{ $kandidat->status_seleksi }}">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <strong class="kanban-title" style="cursor: pointer;">{{ $kandidat->nama_lengkap }}</strong>
                                    </div>

                                    <div class="kanban-meta mt-2">
                                        @if($kandidat->nama_panggilan)
                                            <span><i class="fa fa-user"></i> {{ $kandidat->nama_panggilan }}</span>
                                        @endif
                                        @if($kandidat->nik_ktp)
                                            <span><i class="fa fa-id-card"></i> {{ $kandidat->nik_ktp }}</span>
                                        @endif
                                        @if($kandidat->nomor_hp)
                                            <span><i class="fa fa-phone"></i> {{ $kandidat->nomor_hp }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>

                        @if ($canCreate)
                            <button type="button" class="btn btn-sm btn-light w-100 mt-2 btn-add-kandidat" data-status="{{ $key }}" data-ads-id="{{ $board['ads']->id }}">
                                <i class="fa fa-plus"></i> Tambah Kandidat
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            <i class="fa fa-info-circle me-2"></i>
            Tidak ada iklan lowongan yang aktif.
            @if(check_access('ads.create'))
                <a href="{{ route('ads.create') }}">Buat iklan lowongan terlebih dahulu.</a>
            @endif
        </div>
    @endforelse

    <!-- Modal Tambah Kandidat -->
    <div class="modal fade" id="kandidatModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kandidatModalTitle">
                        <i class="fa-solid fa-user-plus me-2"></i>Tambah Kandidat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="kandidatForm">
                        <input type="hidden" id="kandidat_id">
                        <input type="hidden" id="ads_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Panggilan</label>
                                <input type="text" class="form-control" id="nama_panggilan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIK KTP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nik_ktp" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" id="nomor_hp">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="email">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Status Seleksi</label>
                                <select class="form-select" id="status_seleksi">
                                    <option value="open" selected>Open</option>
                                    <option value="interview">Interview</option>
                                    <option value="tes">Tes</option>
                                    <option value="dipanggil">Dipanggil</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    @if($canDelete)
                        <button type="button" class="btn btn-danger" id="btn-delete-kandidat" style="display: none;">Hapus Kandidat</button>
                    @endif
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" id="btn-save-kandidat">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3" id="kanban-toast-container" style="z-index: 1200;"></div>
@endsection

@section('style')
    <style>
        .kanban-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            gap: 12px;
            flex-wrap: wrap;
        }

        #kanban-board.kanban-board {
            display: flex;
            align-items: stretch;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 8px;
            width: 100% !important;
            min-width: 100%;
            margin-bottom: 0;
            box-shadow: none;
            border-radius: 0;
            cursor: default;
        }

        .kanban-column {
            background: #f6f7fb;
            border-radius: 10px;
            border: 1px solid #e8e8ef;
            padding: 10px;
            min-height: 420px;
            display: flex;
            flex-direction: column;
            flex: 1 1 320px;
            min-width: 280px;
        }

        .kanban-column-head {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 8px;
            gap: 8px;
        }

        .kanban-cards {
            min-height: 120px;
            flex: 1;
        }

        .kanban-card {
            background: #fff;
            border: 1px solid #eaebf2;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            cursor: grab;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);
        }

        .kanban-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .kanban-card.dragging {
            opacity: 0.5;
        }

        .kanban-card.drag-over {
            border: 2px dashed #4f46e5;
            background: #f0f0ff;
        }

        .kanban-title {
            font-size: 14px;
            color: #333;
            line-height: 1.3;
        }

        .kanban-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 12px;
            color: #6b7280;
        }

        .kanban-meta i {
            width: 16px;
            color: #9ca3af;
        }

        .toast {
            min-width: 260px;
        }
    </style>
@endsection

@section('script')
    <script>
        let csrfToken = '{{ csrf_token() }}';
        let rekrutmentMoveUrl = '{{ route('rekrutment.move', ['id' => '__ID__']) }}?v=2';
        let rekrutmentShowUrl = '{{ route('rekrutment.show', ['id' => '__ID__']) }}?v=2';

        document.addEventListener('DOMContentLoaded', function() {
            initializeKanbanBoards();
        });

        function initializeKanbanBoards() {
            const cards = document.querySelectorAll('.kanban-card');
            const dropZones = document.querySelectorAll('.kanban-cards');

            cards.forEach(card => {
                card.addEventListener('dragstart', handleDragStart);
                card.addEventListener('dragend', handleDragEnd);
                card.addEventListener('click', handleCardClick);
            });

            dropZones.forEach(zone => {
                zone.addEventListener('dragover', handleDragOver);
                zone.addEventListener('dragleave', handleDragLeave);
                zone.addEventListener('drop', handleDrop);
            });

            document.querySelectorAll('.btn-add-kandidat').forEach(btn => {
                btn.addEventListener('click', function() {
                    const status = this.dataset.status;
                    const adsId = this.dataset.adsId;
                    openModal(null, status, adsId);
                });
            });
        }

        let draggedCard = null;
        let sourceStatus = null;

        function handleDragStart(e) {
            draggedCard = this;
            sourceStatus = this.dataset.status;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        }

        function handleDragEnd(e) {
            this.classList.remove('dragging');
            document.querySelectorAll('.kanban-card').forEach(card => {
                card.classList.remove('drag-over');
            });
            draggedCard = null;
            sourceStatus = null;
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.querySelectorAll('.kanban-card').forEach(card => {
                if (card !== draggedCard) {
                    card.classList.add('drag-over');
                }
            });
        }

        function handleDragLeave(e) {
            this.querySelectorAll('.kanban-card').forEach(card => {
                card.classList.remove('drag-over');
            });
        }

        function handleDrop(e) {
            e.preventDefault();
            const targetStatus = this.dataset.status;

            if (draggedCard && targetStatus !== sourceStatus) {
                this.appendChild(draggedCard);
                draggedCard.dataset.status = targetStatus;

                // Update count badges
                updateColumnCounts(this);

                // Send update to server
                updateKandidatStatus(draggedCard.dataset.id, targetStatus);
            }

            this.querySelectorAll('.kanban-card').forEach(card => {
                card.classList.remove('drag-over');
            });
        }

        function updateColumnCounts(cardsContainer) {
            const column = cardsContainer.closest('.kanban-column');
            const countSpan = column.querySelector('.kanban-column-head small');
            const count = cardsContainer.querySelectorAll('.kanban-card').length;
            countSpan.textContent = count + ' kandidat';
        }

        function updateKandidatStatus(id, status) {
            const url = rekrutmentMoveUrl.replace('__ID__', id);
            console.log('Moving kandidat:', id, 'to status:', status, 'URL:', url);
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ status_seleksi: status })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                showToast('Status kandidat berhasil diperbarui', 'success');
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Gagal memperbarui status', 'error');
            });
        }

        function handleCardClick(e) {
            const id = this.dataset.id;
            const url = rekrutmentShowUrl.replace('__ID__', id);
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    openModal(data.kandidat, data.kandidat.status_seleksi, data.kandidat.ads_id);
                });
        }

        function openModal(kandidat, status = 'open', adsId = null) {
            const modal = new bootstrap.Modal(document.getElementById('kandidatModal'));
            const title = document.getElementById('kandidatModalTitle');
            const deleteBtn = document.getElementById('btn-delete-kandidat');

            if (kandidat) {
                title.innerHTML = '<i class="fa-solid fa-user-edit me-2"></i>Edit Kandidat';
                document.getElementById('kandidat_id').value = kandidat.id;
                document.getElementById('nama_lengkap').value = kandidat.nama_lengkap || '';
                document.getElementById('nama_panggilan').value = kandidat.nama_panggilan || '';
                document.getElementById('nik_ktp').value = kandidat.nik_ktp || '';
                document.getElementById('nomor_hp').value = kandidat.nomor_hp || '';
                document.getElementById('email').value = kandidat.email || '';
                document.getElementById('status_seleksi').value = kandidat.status_seleksi || 'open';
                document.getElementById('ads_id').value = kandidat.ads_id || adsId;
                deleteBtn.style.display = 'block';
            } else {
                title.innerHTML = '<i class="fa-solid fa-user-plus me-2"></i>Tambah Kandidat';
                document.getElementById('kandidatForm').reset();
                document.getElementById('kandidat_id').value = '';
                document.getElementById('status_seleksi').value = status;
                document.getElementById('ads_id').value = adsId;
                deleteBtn.style.display = 'none';
            }

            modal.show();
        }

        document.getElementById('btn-save-kandidat').addEventListener('click', function() {
            const id = document.getElementById('kandidat_id').value;
            const adsId = document.getElementById('ads_id').value;
            const data = {
                ads_id: adsId,
                nama_lengkap: document.getElementById('nama_lengkap').value,
                nama_panggilan: document.getElementById('nama_panggilan').value,
                nik_ktp: document.getElementById('nik_ktp').value,
                nomor_hp: document.getElementById('nomor_hp').value,
                email: document.getElementById('email').value,
                status_seleksi: document.getElementById('status_seleksi').value
            };

            if (!data.nama_lengkap || !data.nik_ktp || !data.ads_id) {
                showToast('Nama Lengkap, NIK KTP, dan Iklan Lowongan wajib diisi', 'error');
                return;
            }

            const url = id ? '{{ url('rekrutment') }}/' + id : '{{ route('rekrutment.store') }}';
            const method = id ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                showToast(result.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('kandidatModal')).hide();
                setTimeout(() => window.location.reload(), 500);
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan', 'error');
            });
        });

        document.getElementById('btn-delete-kandidat').addEventListener('click', function() {
            if (!confirm('Apakah Anda yakin ingin menghapus kandidat ini?')) return;

            const id = document.getElementById('kandidat_id').value;
            fetch('{{ url('rekrutment') }}/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(result => {
                showToast(result.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('kandidatModal')).hide();
                setTimeout(() => window.location.reload(), 500);
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Gagal menghapus kandidat', 'error');
            });
        });

        function showToast(message, type = 'success') {
            const container = document.getElementById('kanban-toast-container');
            const toastEl = document.createElement('div');
            const headerClass = type === 'danger' ? 'text-bg-danger' : (type === 'warning' ? 'text-bg-warning' : 'text-bg-success');
            toastEl.className = 'toast align-items-center border-0';
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `
                <div class="d-flex ${headerClass}">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            container.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, { delay: 2200 });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }
    </script>
@endsection
