@extends('template.root')
@section('page-name', 'List Tugas')
@section('title-page', 'Kanban Board List Tugas')

@section('content')
    @php
        $canCreate = check_access('list-tugas.create');
        $canUpdate = check_access('list-tugas.update');
        $canDelete = check_access('list-tugas.delete');
    @endphp
    <div class="kanban-toolbar">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="GET" action="{{ route('list-tugas.index') }}" class="d-flex gap-2 align-items-center">
                <label class="mb-0 fw-semibold">Board</label>
                <select name="board_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 220px;">
                    @foreach ($boards as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $board->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
            </form>
            @if ($canCreate)
                <button type="button" class="btn btn-sm btn-primary" id="btn-new-board">
                    <i class="fa fa-plus"></i> Board Baru
                </button>
            @endif
        </div>

        <div class="kanban-toolbar-info text-muted">
            <small>{{ $board->description ?: 'Kelola tugas dengan drag & drop antar kolom.' }}</small>
        </div>
    </div>

    <div class="kanban-board" id="kanban-board" data-board-id="{{ $board->id }}">
        @foreach ($board->columns as $column)
            <div class="kanban-column" data-column-id="{{ $column->id }}">
                <div class="kanban-column-head">
                    <div>
                        <h6 class="mb-0">{{ $column->name }}</h6>
                        <small class="text-muted">{{ $column->cards->count() }} kartu</small>
                    </div>
                    @if ($canUpdate || $canDelete)
                        <div class="d-flex gap-1">
                            @if ($canUpdate)
                                <button type="button" class="btn btn-xs btn-outline-primary btn-rename-column" data-column-id="{{ $column->id }}" data-column-name="{{ $column->name }}">
                                    <i class="fa fa-pen"></i>
                                </button>
                            @endif
                            @if ($canDelete)
                                <button type="button" class="btn btn-xs btn-outline-danger btn-delete-column" data-column-id="{{ $column->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="kanban-cards" data-column-id="{{ $column->id }}">
                    @foreach ($column->cards as $card)
                        @php
                            $doneCount = $card->checklistItems->where('is_done', true)->count();
                            $allCount = $card->checklistItems->count();
                            $labels = collect(explode(',', (string) $card->labels))->map(fn($v) => trim($v))->filter();
                            $priorityMap = [
                                'low' => 'success',
                                'medium' => 'primary',
                                'high' => 'warning',
                                'urgent' => 'danger',
                            ];
                            $priorityClass = $priorityMap[$card->priority] ?? 'secondary';
                        @endphp
                        <div class="kanban-card" draggable="{{ $canUpdate ? 'true' : 'false' }}" data-card-id="{{ $card->id }}" data-column-id="{{ $column->id }}">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <strong class="kanban-title">{{ $card->title }}</strong>
                                <span class="badge bg-{{ $priorityClass }}">{{ strtoupper($card->priority) }}</span>
                            </div>

                            @if ($labels->count())
                                <div class="kanban-labels mt-2">
                                    @foreach ($labels as $label)
                                        <span class="badge bg-light text-dark border">{{ $label }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="kanban-meta mt-2">
                                @if ($card->due_date)
                                    <span><i class="fa fa-calendar"></i> {{ $card->due_date->format('d M Y') }}</span>
                                @endif

                                @if ($allCount > 0)
                                    <span><i class="fa fa-list-check"></i> {{ $doneCount }}/{{ $allCount }}</span>
                                @endif

                                <span><i class="fa fa-comment"></i> {{ $card->comments->count() }}</span>
                            </div>

                            @if ($card->members->count())
                                <div class="kanban-members mt-2">
                                    @foreach ($card->members->take(4) as $member)
                                        <span class="member-avatar" title="{{ $member->nm_user }}">{{ strtoupper(mb_substr($member->nm_user, 0, 1)) }}</span>
                                    @endforeach
                                    @if ($card->members->count() > 4)
                                        <span class="member-avatar more">+{{ $card->members->count() - 4 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($canCreate)
                    <button type="button" class="btn btn-sm btn-light w-100 mt-2 btn-new-card" data-column-id="{{ $column->id }}">
                        <i class="fa fa-plus"></i> Tambah Kartu
                    </button>
                @endif
            </div>
        @endforeach

        @if ($canCreate)
            <div class="kanban-column kanban-column-add">
                <button class="btn btn-outline-primary w-100" id="btn-add-column">
                    <i class="fa fa-plus"></i> Tambah Kolom
                </button>
            </div>
        @endif
    </div>

    <div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cardModalTitle">Detail Kartu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cardForm">
                        <input type="hidden" id="card_id">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Judul</label>
                                <input type="text" class="form-control" id="title" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kolom</label>
                                <select class="form-select" id="column_id" required>
                                    @foreach ($board->columns as $column)
                                        <option value="{{ $column->id }}">{{ $column->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prioritas</label>
                                <select class="form-select" id="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mulai</label>
                                <input type="date" class="form-control" id="start_date">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Deadline</label>
                                <input type="date" class="form-control" id="due_date">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Label (pisah koma)</label>
                                <input type="text" class="form-control" id="labels" placeholder="contoh: Finance, Urgent, Internal">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Penanggung Jawab</label>
                                <select class="form-select" id="assignee_ids" multiple>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id_user }}">{{ $user->nm_user }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" rows="4"></textarea>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <h6 class="mb-2">Checklist</h6>
                            <div class="d-flex gap-2 mb-2">
                                <input type="text" class="form-control form-control-sm" id="checklist_input" placeholder="Tambahkan checklist...">
                                <button class="btn btn-sm btn-primary" type="button" id="btn-add-checklist">Tambah</button>
                            </div>
                            <div id="checklist-list" class="checklist-list"></div>
                        </div>
                        <div class="col-md-12">
                            <h6 class="mb-2">Komentar</h6>
                            <div class="d-flex gap-2 mb-2">
                                <input type="text" class="form-control form-control-sm" id="comment_input" placeholder="Tambahkan komentar...">
                                <button class="btn btn-sm btn-primary" type="button" id="btn-add-comment">Kirim</button>
                            </div>
                            <div id="comment-list" class="comment-list"></div>
                        </div>
                    </div>

                    <hr>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <h6 class="mb-2">Lampiran</h6>
                            <div class="d-flex gap-2 mb-2">
                                <input type="file" class="form-control form-control-sm" id="attachment_input">
                                <button class="btn btn-sm btn-primary" type="button" id="btn-add-attachment">Upload</button>
                            </div>
                            <div id="attachment-list" class="attachment-list"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" id="btn-delete-card" style="display: none;">Hapus Kartu</button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" id="btn-save-card">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('list-tugas.board.store') }}" id="newBoardForm" style="display:none;">
        @csrf
        <input type="hidden" name="name" id="new_board_name">
        <input type="hidden" name="description" id="new_board_description">
    </form>

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

        .kanban-column-add {
            justify-content: center;
            min-height: 180px;
            background: #fff;
            border: 1px dashed #c9ccdb;
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

        .kanban-card.dragging {
            opacity: 0.5;
        }

        .kanban-title {
            cursor: pointer;
            line-height: 1.3;
        }

        .kanban-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 12px;
            color: #6b7280;
        }

        .kanban-members {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .member-avatar {
            width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #4f46e5;
            color: #fff;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .member-avatar.more {
            background: #6b7280;
        }

        .checklist-item,
        .comment-item,
        .attachment-item,
        .activity-item {
            padding: 8px;
            border: 1px solid #ececf4;
            border-radius: 8px;
            margin-bottom: 6px;
            background: #fff;
            font-size: 13px;
        }

        .activity-item small,
        .comment-item small {
            color: #6b7280;
        }

        .select2-container {
            width: 100% !important;
        }

        .toast {
            min-width: 260px;
        }

        .attachment-preview {
            width: 120px;
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #ececf4;
            object-fit: cover;
            margin-top: 6px;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('cuba/js/editor/ckeditor/ckeditor.js') }}"></script>
    <script>
        const boardId = Number(document.getElementById('kanban-board').dataset.boardId);
        const csrfToken = '{{ csrf_token() }}';
        const canCreate = @json((bool) $canCreate);
        const canUpdate = @json((bool) $canUpdate);
        const canDelete = @json((bool) $canDelete);
        const modalEl = document.getElementById('cardModal');
        const cardModal = new bootstrap.Modal(modalEl);
        const assigneeSelect = $('#assignee_ids');
        let descriptionEditor = null;
        let currentCardId = null;
        let activeDraggedCard = null;

        const routeTemplate = {
            storeColumn: "{{ url('list-tugas/board/' . $board->id . '/columns') }}",
            updateColumn: "{{ url('list-tugas/columns/__ID__') }}",
            deleteColumn: "{{ url('list-tugas/columns/__ID__') }}",
            storeCard: "{{ url('list-tugas/board/' . $board->id . '/cards') }}",
            showCard: "{{ url('list-tugas/cards/__ID__/detail') }}",
            updateCard: "{{ url('list-tugas/cards/__ID__') }}",
            deleteCard: "{{ url('list-tugas/cards/__ID__') }}",
            moveCard: "{{ url('list-tugas/cards/__ID__/move') }}",
            storeChecklist: "{{ url('list-tugas/cards/__ID__/checklist') }}",
            toggleChecklist: "{{ url('list-tugas/checklist/__ID__/toggle') }}",
            deleteChecklist: "{{ url('list-tugas/checklist/__ID__') }}",
            storeComment: "{{ url('list-tugas/cards/__ID__/comments') }}",
            deleteComment: "{{ url('list-tugas/comments/__ID__') }}",
            storeAttachment: "{{ url('list-tugas/cards/__ID__/attachments') }}",
            deleteAttachment: "{{ url('list-tugas/attachments/__ID__') }}"
        };

        function endpoint(template, id) {
            return template.replace('__ID__', id);
        }

        function normalizeDateForInput(value) {
            if (!value) return '';
            if (typeof value === 'string') {
                if (/^\d{4}-\d{2}-\d{2}$/.test(value)) return value;
                const isoMatch = value.match(/^(\d{4}-\d{2}-\d{2})T/);
                if (isoMatch) return isoMatch[1];
            }
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '';
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function api(url, method, payload = null) {
            return fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: payload ? JSON.stringify(payload) : null
            }).then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan.');
                }
                return data;
            });
        }

        function apiFormData(url, method, formData) {
            return fetch(url, {
                method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan.');
                }
                return data;
            });
        }

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

        function formatDateDisplay(value) {
            const normalized = normalizeDateForInput(value);
            if (!normalized) return '';
            const date = new Date(`${normalized}T00:00:00`);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function updateColumnCardCounts() {
            document.querySelectorAll('.kanban-column').forEach((column) => {
                const count = column.querySelectorAll('.kanban-card').length;
                const counter = column.querySelector('.kanban-column-head small');
                if (counter) {
                    counter.textContent = `${count} kartu`;
                }
            });
        }

        function attachCardClickHandler(cardEl) {
            cardEl.addEventListener('click', function(e) {
                const skipClickSelector = 'button, a, input, select, textarea, label, .btn';
                if (e.target.closest(skipClickSelector)) return;

                const cardId = Number(this.dataset.cardId);
                if (!cardId) return;
                openCard(cardId);
            });
        }

        function attachCardDragHandler(cardEl) {
            cardEl.addEventListener('dragstart', function() {
                activeDraggedCard = this;
                this.classList.add('dragging');
            });

            cardEl.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                activeDraggedCard = null;
            });
        }

        function renderCardPriorityClass(priority) {
            const map = {
                low: 'success',
                medium: 'primary',
                high: 'warning',
                urgent: 'danger',
            };
            return map[priority] || 'secondary';
        }

        function buildCardElement(card) {
            const labels = (card.labels || '')
                .split(',')
                .map((v) => v.trim())
                .filter(Boolean);

            const memberHtml = (card.members || []).length
                ? `<div class="kanban-members mt-2">
                    ${(card.members || []).slice(0, 4).map((member) => `<span class="member-avatar" title="${member.nm_user}">${(member.nm_user || 'U').charAt(0).toUpperCase()}</span>`).join('')}
                    ${(card.members || []).length > 4 ? `<span class="member-avatar more">+${(card.members || []).length - 4}</span>` : ''}
                </div>`
                : '';

            const dueText = formatDateDisplay(card.due_date);
            const checklistItems = card.checklist_items || [];
            const doneCount = checklistItems.filter((item) => !!item.is_done).length;
            const allCount = checklistItems.length;

            const cardEl = document.createElement('div');
            cardEl.className = 'kanban-card';
            cardEl.setAttribute('draggable', canUpdate ? 'true' : 'false');
            cardEl.dataset.cardId = String(card.id);
            cardEl.dataset.columnId = String(card.column_id);
            cardEl.innerHTML = `
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <strong class="kanban-title">${card.title || '-'}</strong>
                    <span class="badge bg-${renderCardPriorityClass(card.priority)}">${String(card.priority || 'medium').toUpperCase()}</span>
                </div>
                ${labels.length ? `<div class="kanban-labels mt-2">${labels.map((label) => `<span class="badge bg-light text-dark border">${label}</span>`).join('')}</div>` : ''}
                <div class="kanban-meta mt-2">
                    ${dueText ? `<span><i class="fa fa-calendar"></i> ${dueText}</span>` : ''}
                    ${allCount > 0 ? `<span><i class="fa fa-list-check"></i> ${doneCount}/${allCount}</span>` : ''}
                    <span><i class="fa fa-comment"></i> ${(card.comments || []).length}</span>
                </div>
                ${memberHtml}
            `;

            attachCardClickHandler(cardEl);
            if (canUpdate) {
                attachCardDragHandler(cardEl);
            }

            return cardEl;
        }

        function upsertCardInBoard(card) {
            const existing = document.querySelector(`.kanban-card[data-card-id="${card.id}"]`);
            const targetColumnBody = document.querySelector(`.kanban-cards[data-column-id="${card.column_id}"]`);
            if (!targetColumnBody) return;

            const newCardEl = buildCardElement(card);

            if (existing) {
                if (existing.parentElement === targetColumnBody) {
                    existing.replaceWith(newCardEl);
                } else {
                    existing.remove();
                    targetColumnBody.appendChild(newCardEl);
                }
            } else {
                targetColumnBody.appendChild(newCardEl);
            }
        }

        function collectCardPayload() {
            return {
                column_id: Number(document.getElementById('column_id').value),
                title: document.getElementById('title').value.trim(),
                description: descriptionEditor ? descriptionEditor.getData() : document.getElementById('description').value,
                priority: document.getElementById('priority').value,
                start_date: document.getElementById('start_date').value || null,
                due_date: document.getElementById('due_date').value || null,
                labels: document.getElementById('labels').value,
                assignee_ids: [...document.getElementById('assignee_ids').selectedOptions].map((opt) => Number(opt.value))
            };
        }

        async function ensureCardExistsForDetailActions() {
            if (currentCardId) return true;
            if (!canCreate) return false;

            const payload = collectCardPayload();
            if (!payload.title) {
                alert('Isi judul kartu terlebih dahulu sebelum menambah checklist atau komentar.');
                return false;
            }

            const storeResult = await api(routeTemplate.storeCard, 'POST', payload);
            const newCardId = Number(storeResult.card_id);
            if (!newCardId) return false;

            currentCardId = newCardId;
            const detailResult = await api(endpoint(routeTemplate.showCard, newCardId), 'GET');
            upsertCardInBoard(detailResult.card);
            updateColumnCardCounts();
            document.getElementById('card_id').value = String(newCardId);
            document.getElementById('cardModalTitle').textContent = 'Detail Kartu';
            document.getElementById('btn-delete-card').style.display = canDelete ? 'inline-block' : 'none';
            showToast('Kartu otomatis disimpan, lanjutkan tambah checklist/komentar.');
            return true;
        }

        assigneeSelect.select2({
            placeholder: 'Pilih penanggung jawab',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#cardModal')
        });

        if (window.CKEDITOR && document.getElementById('description')) {
            descriptionEditor = CKEDITOR.replace('description', {
                height: 180,
                toolbar: [
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'RemoveFormat'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList'] },
                    { name: 'links', items: ['Link', 'Unlink'] },
                    { name: 'clipboard', items: ['Undo', 'Redo'] }
                ],
                removePlugins: 'elementspath',
                resize_enabled: false
            });
        }

        const btnNewBoard = document.getElementById('btn-new-board');
        if (btnNewBoard) btnNewBoard.addEventListener('click', function() {
            const name = prompt('Nama board baru:');
            if (!name) return;

            const description = prompt('Deskripsi board (opsional):') || '';
            document.getElementById('new_board_name').value = name;
            document.getElementById('new_board_description').value = description;
            document.getElementById('newBoardForm').submit();
        });

        const btnAddColumn = document.getElementById('btn-add-column');
        if (btnAddColumn) btnAddColumn.addEventListener('click', async function() {
            const name = prompt('Nama kolom baru:');
            if (!name) return;

            try {
                await api(routeTemplate.storeColumn, 'POST', { name });
                location.reload();
            } catch (error) {
                alert(error.message);
            }
        });

        document.querySelectorAll('.btn-rename-column').forEach((btn) => {
            btn.addEventListener('click', async function() {
                const columnId = this.dataset.columnId;
                const oldName = this.dataset.columnName;
                const name = prompt('Ubah nama kolom:', oldName);
                if (!name) return;

                try {
                    await api(endpoint(routeTemplate.updateColumn, columnId), 'PUT', { name });
                    location.reload();
                } catch (error) {
                    alert(error.message);
                }
            });
        });

        document.querySelectorAll('.btn-delete-column').forEach((btn) => {
            btn.addEventListener('click', async function() {
                const columnId = this.dataset.columnId;
                if (!confirm('Hapus kolom ini?')) return;

                try {
                    await api(endpoint(routeTemplate.deleteColumn, columnId), 'DELETE');
                    location.reload();
                } catch (error) {
                    alert(error.message);
                }
            });
        });

        document.querySelectorAll('.btn-new-card').forEach((btn) => {
            btn.addEventListener('click', function() {
                openCreateCard(this.dataset.columnId);
            });
        });

        document.querySelectorAll('.kanban-card').forEach((card) => attachCardClickHandler(card));

        function initDragDrop() {
            if (!canUpdate) return;
            document.querySelectorAll('.kanban-card').forEach((card) => {
                attachCardDragHandler(card);
            });

            document.querySelectorAll('.kanban-cards').forEach((columnBody) => {
                columnBody.addEventListener('dragover', function(e) {
                    e.preventDefault();

                    if (!activeDraggedCard) return;

                    const afterElement = getDragAfterElement(this, e.clientY);
                    if (afterElement == null) {
                        this.appendChild(activeDraggedCard);
                    } else {
                        this.insertBefore(activeDraggedCard, afterElement);
                    }
                });

                columnBody.addEventListener('drop', async function(e) {
                    e.preventDefault();
                    if (!activeDraggedCard) return;

                    this.appendChild(activeDraggedCard);

                    const cardId = Number(activeDraggedCard.dataset.cardId);
                    const columnId = Number(this.dataset.columnId);
                    const cards = [...this.querySelectorAll('.kanban-card')];
                    const position = cards.findIndex((item) => Number(item.dataset.cardId) === cardId) + 1;

                    try {
                        await api(endpoint(routeTemplate.moveCard, cardId), 'POST', {
                            column_id: columnId,
                            position
                        });
                        updateColumnCardCounts();
                        showToast('Kartu berhasil dipindahkan.');
                    } catch (error) {
                        alert(error.message);
                        location.reload();
                    }
                });
            });
        }

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.kanban-card:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset, element: child };
                }

                return closest;
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        function resetCardForm() {
            currentCardId = null;
            document.getElementById('card_id').value = '';
            document.getElementById('title').value = '';
            if (descriptionEditor) {
                descriptionEditor.setData('');
            } else {
                document.getElementById('description').value = '';
            }
            document.getElementById('priority').value = 'medium';
            document.getElementById('start_date').value = '';
            document.getElementById('due_date').value = '';
            document.getElementById('labels').value = '';
            [...document.getElementById('assignee_ids').options].forEach((option) => option.selected = false);
            assigneeSelect.trigger('change');
            document.getElementById('checklist-list').innerHTML = '';
            document.getElementById('comment-list').innerHTML = '';
            document.getElementById('attachment-list').innerHTML = '';
            document.getElementById('attachment_input').value = '';
            const activityListEl = document.getElementById('activity-list');
            if (activityListEl) {
                activityListEl.innerHTML = '';
            }
            document.getElementById('btn-delete-card').style.display = 'none';
            document.getElementById('btn-save-card').style.display = (canCreate || canUpdate) ? 'inline-block' : 'none';
        }

        function openCreateCard(columnId) {
            if (!canCreate) return;
            resetCardForm();
            document.getElementById('cardModalTitle').textContent = 'Tambah Kartu';
            document.getElementById('column_id').value = String(columnId);
            cardModal.show();
        }

        async function openCard(cardId) {
            resetCardForm();
            currentCardId = Number(cardId);
            document.getElementById('cardModalTitle').textContent = 'Detail Kartu';
            document.getElementById('btn-delete-card').style.display = canDelete ? 'inline-block' : 'none';

            try {
                const data = await api(endpoint(routeTemplate.showCard, cardId), 'GET');
                const card = data.card;

                document.getElementById('card_id').value = card.id;
                document.getElementById('title').value = card.title || '';
                document.getElementById('column_id').value = String(card.column_id);
                document.getElementById('priority').value = card.priority || 'medium';
                document.getElementById('start_date').value = normalizeDateForInput(card.start_date);
                document.getElementById('due_date').value = normalizeDateForInput(card.due_date);
                document.getElementById('labels').value = card.labels || '';
                if (descriptionEditor) {
                    descriptionEditor.setData(card.description || '');
                } else {
                    document.getElementById('description').value = card.description || '';
                }

                const selectedMembers = new Set((card.members || []).map((item) => String(item.id_user)));
                [...document.getElementById('assignee_ids').options].forEach((option) => {
                    option.selected = selectedMembers.has(option.value);
                });
                assigneeSelect.trigger('change');

                renderChecklist(card.checklist_items || []);
                renderComments(card.comments || []);
                renderAttachments(card.attachments || []);
                renderActivities(data.activities || []);
                cardModal.show();
            } catch (error) {
                alert(error.message);
            }
        }

        function renderChecklist(items) {
            const wrapper = document.getElementById('checklist-list');
            wrapper.innerHTML = '';

            if (!items.length) {
                wrapper.innerHTML = '<small class="text-muted">Belum ada checklist.</small>';
                return;
            }

            items.forEach((item) => {
                const row = document.createElement('div');
                row.className = 'checklist-item d-flex justify-content-between align-items-center gap-2';
                row.innerHTML = `
                    <label class="d-flex align-items-center gap-2 mb-0 flex-grow-1">
                        <input type="checkbox" ${item.is_done ? 'checked' : ''} onchange="toggleChecklist(${item.id}, this.checked)">
                        <span>${item.title}</span>
                    </label>
                    <button class="btn btn-xs btn-outline-danger" onclick="deleteChecklist(${item.id})"><i class="fa fa-trash"></i></button>
                `;
                wrapper.appendChild(row);
            });
        }

        function renderComments(items) {
            const wrapper = document.getElementById('comment-list');
            wrapper.innerHTML = '';

            if (!items.length) {
                wrapper.innerHTML = '<small class="text-muted">Belum ada komentar.</small>';
                return;
            }

            items.forEach((item) => {
                const row = document.createElement('div');
                row.className = 'comment-item';
                row.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <strong>${item.user ? item.user.nm_user : 'User'}</strong>
                        ${canDelete ? `<button class="btn btn-xs btn-outline-danger" onclick="deleteComment(${item.id})"><i class="fa fa-trash"></i></button>` : ''}
                    </div>
                    <div>${item.comment}</div>
                    <small>${new Date(item.created_at).toLocaleString('id-ID')}</small>
                `;
                wrapper.appendChild(row);
            });
        }

        function renderAttachments(items) {
            const wrapper = document.getElementById('attachment-list');
            wrapper.innerHTML = '';

            if (!items.length) {
                wrapper.innerHTML = '<small class="text-muted">Belum ada lampiran.</small>';
                return;
            }

            items.forEach((item) => {
                const isImage = typeof item.mime_type === 'string' && item.mime_type.toLowerCase().startsWith('image/');
                const row = document.createElement('div');
                row.className = 'attachment-item d-flex justify-content-between align-items-center gap-2';
                row.innerHTML = `
                    <div class="d-flex flex-column">
                        <a href="${item.file_url}" target="_blank" rel="noopener noreferrer">${item.original_name}</a>
                        <small>${item.file_size_text || ''}</small>
                        ${isImage ? `<img src="${item.file_url}" alt="${item.original_name}" class="attachment-preview">` : ''}
                    </div>
                    ${canUpdate ? `<button class="btn btn-xs btn-outline-danger" onclick="deleteAttachment(${item.id})"><i class="fa fa-trash"></i></button>` : ''}
                `;
                wrapper.appendChild(row);
            });
        }

        function renderActivities(items) {
            const wrapper = document.getElementById('activity-list');
            if (!wrapper) return;
            wrapper.innerHTML = '';

            if (!items.length) {
                wrapper.innerHTML = '<small class="text-muted">Belum ada aktivitas.</small>';
                return;
            }

            items.forEach((item) => {
                const row = document.createElement('div');
                row.className = 'activity-item';
                row.innerHTML = `
                    <div><strong>${item.user ? item.user.nm_user : 'System'}</strong> - ${item.description || item.action}</div>
                    <small>${new Date(item.created_at).toLocaleString('id-ID')}</small>
                `;
                wrapper.appendChild(row);
            });
        }

        document.getElementById('btn-save-card').addEventListener('click', async function() {
            if (!canCreate && !canUpdate) return;
            const payload = collectCardPayload();

            try {
                if (currentCardId) {
                    if (!canUpdate) return;
                    await api(endpoint(routeTemplate.updateCard, currentCardId), 'PUT', payload);
                    const detailResult = await api(endpoint(routeTemplate.showCard, currentCardId), 'GET');
                    upsertCardInBoard(detailResult.card);
                    cardModal.hide();
                    updateColumnCardCounts();
                    showToast('Kartu berhasil diperbarui.');
                } else {
                    if (!canCreate) return;
                    const storeResult = await api(routeTemplate.storeCard, 'POST', payload);
                    const newCardId = Number(storeResult.card_id);
                    if (newCardId) {
                        const detailResult = await api(endpoint(routeTemplate.showCard, newCardId), 'GET');
                        const newCard = detailResult.card;
                        const targetColumnId = Number(newCard.column_id);
                        const columnBody = document.querySelector(`.kanban-cards[data-column-id="${targetColumnId}"]`);
                        if (columnBody) {
                            const cardEl = buildCardElement(newCard);
                            columnBody.appendChild(cardEl);
                        }
                        updateColumnCardCounts();
                    }
                    cardModal.hide();
                    showToast('Kartu berhasil dibuat.');
                }
            } catch (error) {
                alert(error.message);
            }
        });

        document.getElementById('btn-delete-card').addEventListener('click', async function() {
            if (!canDelete) return;
            if (!currentCardId) return;
            if (!confirm('Hapus kartu ini?')) return;

            try {
                await api(endpoint(routeTemplate.deleteCard, currentCardId), 'DELETE');
                const cardEl = document.querySelector(`.kanban-card[data-card-id="${currentCardId}"]`);
                if (cardEl) {
                    cardEl.remove();
                }
                cardModal.hide();
                updateColumnCardCounts();
                showToast('Kartu berhasil dihapus.');
            } catch (error) {
                alert(error.message);
            }
        });

        document.getElementById('btn-add-checklist').addEventListener('click', async function() {
            if (!canUpdate) return;
            const title = document.getElementById('checklist_input').value.trim();
            if (!title) return;

            try {
                const isReady = await ensureCardExistsForDetailActions();
                if (!isReady) return;
                await api(endpoint(routeTemplate.storeChecklist, currentCardId), 'POST', { title });
                document.getElementById('checklist_input').value = '';
                await openCard(currentCardId);
            } catch (error) {
                alert(error.message);
            }
        });

        window.toggleChecklist = async function(itemId, isDone) {
            if (!canUpdate) return;
            try {
                await api(endpoint(routeTemplate.toggleChecklist, itemId), 'PATCH', { is_done: isDone });
                await openCard(currentCardId);
            } catch (error) {
                alert(error.message);
            }
        }

        window.deleteChecklist = async function(itemId) {
            if (!canUpdate) return;
            if (!confirm('Hapus checklist ini?')) return;

            try {
                await api(endpoint(routeTemplate.deleteChecklist, itemId), 'DELETE');
                await openCard(currentCardId);
            } catch (error) {
                alert(error.message);
            }
        }

        window.deleteComment = async function(commentId) {
            if (!canDelete) return;
            if (!confirm('Hapus komentar ini?')) return;

            try {
                await api(endpoint(routeTemplate.deleteComment, commentId), 'DELETE');
                await openCard(currentCardId);
            } catch (error) {
                alert(error.message);
            }
        }

        document.getElementById('btn-add-attachment').addEventListener('click', async function() {
            if (!canUpdate) return;

            const input = document.getElementById('attachment_input');
            const file = input.files && input.files[0] ? input.files[0] : null;
            if (!file) {
                alert('Pilih file lampiran terlebih dahulu.');
                return;
            }

            try {
                const isReady = await ensureCardExistsForDetailActions();
                if (!isReady) return;

                const formData = new FormData();
                formData.append('attachment', file);
                await apiFormData(endpoint(routeTemplate.storeAttachment, currentCardId), 'POST', formData);
                input.value = '';
                await openCard(currentCardId);
                showToast('Lampiran berhasil ditambahkan.');
            } catch (error) {
                alert(error.message);
            }
        });

        window.deleteAttachment = async function(attachmentId) {
            if (!canUpdate) return;
            if (!confirm('Hapus lampiran ini?')) return;

            try {
                await api(endpoint(routeTemplate.deleteAttachment, attachmentId), 'DELETE');
                await openCard(currentCardId);
                showToast('Lampiran dihapus.');
            } catch (error) {
                alert(error.message);
            }
        }

        document.getElementById('btn-add-comment').addEventListener('click', async function() {
            if (!canUpdate) return;
            const comment = document.getElementById('comment_input').value.trim();
            if (!comment) return;

            try {
                const isReady = await ensureCardExistsForDetailActions();
                if (!isReady) return;
                await api(endpoint(routeTemplate.storeComment, currentCardId), 'POST', { comment });
                document.getElementById('comment_input').value = '';
                await openCard(currentCardId);
            } catch (error) {
                alert(error.message);
            }
        });

        initDragDrop();
        window.openCard = openCard;
    </script>
@endsection
