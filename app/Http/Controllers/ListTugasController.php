<?php

namespace App\Http\Controllers;

use App\Models\TaskActivity;
use App\Models\TaskBoard;
use App\Models\TaskCard;
use App\Models\TaskCardAttachment;
use App\Models\TaskChecklistItem;
use App\Models\TaskColumn;
use App\Models\TaskComment;
use App\Models\User;
use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ListTugasController extends Controller
{
    use HasAccessControl;

    public function index(Request $request)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.index')) {
            return $denied;
        }

        $this->ensureDefaultBoard();

        $boards = TaskBoard::query()->orderBy('name')->get(['id', 'name']);
        $boardId = $request->integer('board_id') ?: optional($boards->first())->id;

        $board = TaskBoard::query()
            ->with([
                'columns' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'columns.cards' => function ($query) {
                    $query->orderBy('position');
                },
                'columns.cards.members:id_user,nm_user',
                'columns.cards.checklistItems:id,card_id,is_done',
                'columns.cards.comments:id,card_id',
            ])
            ->findOrFail($boardId);

        $users = User::query()
            ->select(['id_user', 'nm_user'])
            ->whereNull('deleted_at')
            ->orderBy('nm_user')
            ->get();

        return view('list-tugas.index', compact('board', 'boards', 'users'));
    }

    public function storeBoard(Request $request)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.create')) {
            return $denied;
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $board = TaskBoard::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by'  => Auth::id(),
            'updated_by'  => Auth::id(),
        ]);

        $defaultColumns = ['Backlog', 'To Do', 'In Progress', 'Done'];

        foreach ($defaultColumns as $index => $columnName) {
            TaskColumn::create([
                'board_id'   => $board->id,
                'name'       => $columnName,
                'sort_order' => $index + 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        return redirect()->route('list-tugas.index', ['board_id' => $board->id])->with('success', 'Board berhasil dibuat.');
    }

    public function storeColumn(Request $request, int $boardId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.create', true)) {
            return $denied;
        }

        $validated = $request->validate([
            'name' => 'required|string|max:80',
        ]);

        $board = TaskBoard::findOrFail($boardId);

        $nextSort = (int) TaskColumn::where('board_id', $board->id)->max('sort_order') + 1;

        $column = TaskColumn::create([
            'board_id'   => $board->id,
            'name'       => $validated['name'],
            'sort_order' => $nextSort,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Kolom berhasil ditambahkan.',
            'column'  => $column,
        ]);
    }

    public function updateColumn(Request $request, int $columnId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.update', true)) {
            return $denied;
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:80',
            'sort_order' => 'nullable|integer|min:1',
        ]);

        $column = TaskColumn::findOrFail($columnId);
        $column->name = $validated['name'];

        if (isset($validated['sort_order'])) {
            $column->sort_order = $validated['sort_order'];
        }

        $column->updated_by = Auth::id();
        $column->save();

        return response()->json(['message' => 'Kolom berhasil diperbarui.']);
    }

    public function destroyColumn(int $columnId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.delete', true)) {
            return $denied;
        }

        $column = TaskColumn::withCount('cards')->findOrFail($columnId);

        if ($column->cards_count > 0) {
            return response()->json(['message' => 'Kolom masih memiliki kartu. Pindahkan kartu terlebih dahulu.'], 422);
        }

        $boardId = $column->board_id;
        $column->delete();

        $this->reorderColumns($boardId);

        return response()->json(['message' => 'Kolom berhasil dihapus.']);
    }

    public function storeCard(Request $request, int $boardId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.create', true)) {
            return $denied;
        }

        $validated = $request->validate([
            'column_id'    => 'required|exists:task_columns,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'priority'     => 'nullable|in:low,medium,high,urgent',
            'start_date'   => 'nullable|date',
            'due_date'     => 'nullable|date',
            'labels'       => 'nullable|string|max:255',
            'assignee_ids' => 'nullable|array',
            'assignee_ids.*' => 'integer|exists:users,id_user',
        ]);

        $column = TaskColumn::where('board_id', $boardId)->findOrFail($validated['column_id']);
        $nextPosition = (int) TaskCard::where('column_id', $column->id)->max('position') + 1;

        $card = TaskCard::create([
            'board_id'     => $boardId,
            'column_id'    => $column->id,
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'priority'     => $validated['priority'] ?? 'medium',
            'start_date'   => $validated['start_date'] ?? null,
            'due_date'     => $validated['due_date'] ?? null,
            'labels'       => $this->normalizeLabels($validated['labels'] ?? null),
            'position'     => $nextPosition,
            'created_by'   => Auth::id(),
            'updated_by'   => Auth::id(),
        ]);

        $card->members()->sync($validated['assignee_ids'] ?? []);

        $this->logActivity($card->id, 'card_created', 'Membuat kartu tugas.', [
            'title'     => $card->title,
            'column_id' => $card->column_id,
        ]);

        return response()->json(['message' => 'Kartu berhasil dibuat.', 'card_id' => $card->id]);
    }

    public function showCard(int $cardId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.index', true)) {
            return $denied;
        }

        $card = TaskCard::with([
            'members:id_user,nm_user',
            'checklistItems:id,card_id,title,is_done,sort_order',
            'comments' => function ($query) {
                $query->with('user:id_user,nm_user')->latest();
            },
            'attachments' => function ($query) {
                $query->with('creator:id_user,nm_user')->latest();
            },
            'column:id,board_id,name',
            'board:id,name',
        ])->findOrFail($cardId);

        $activities = TaskActivity::query()
            ->with('user:id_user,nm_user')
            ->where('card_id', $cardId)
            ->latest()
            ->limit(30)
            ->get();

        return response()->json([
            'card'       => array_merge($card->toArray(), [
                'attachments' => $card->attachments->map(function ($attachment) {
                    return $this->serializeAttachment($attachment);
                })->values(),
            ]),
            'activities' => $activities,
        ]);
    }

    public function updateCard(Request $request, int $cardId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.update', true)) {
            return $denied;
        }

        $validated = $request->validate([
            'column_id'    => 'required|exists:task_columns,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'priority'     => 'required|in:low,medium,high,urgent',
            'start_date'   => 'nullable|date',
            'due_date'     => 'nullable|date',
            'labels'       => 'nullable|string|max:255',
            'assignee_ids' => 'nullable|array',
            'assignee_ids.*' => 'integer|exists:users,id_user',
        ]);

        $card = TaskCard::findOrFail($cardId);
        $oldColumnId = $card->column_id;

        $targetColumn = TaskColumn::where('board_id', $card->board_id)->findOrFail($validated['column_id']);

        if ((int) $targetColumn->id !== (int) $oldColumnId) {
            $card->position = (int) TaskCard::where('column_id', $targetColumn->id)->max('position') + 1;
            $card->column_id = $targetColumn->id;
            $this->reorderCardsInColumn($oldColumnId);
        }

        $card->title = $validated['title'];
        $card->description = $validated['description'] ?? null;
        $card->priority = $validated['priority'];
        $card->start_date = $validated['start_date'] ?? null;
        $card->due_date = $validated['due_date'] ?? null;
        $card->labels = $this->normalizeLabels($validated['labels'] ?? null);
        $card->updated_by = Auth::id();
        $card->save();

        $card->members()->sync($validated['assignee_ids'] ?? []);

        $this->logActivity($card->id, 'card_updated', 'Memperbarui kartu tugas.', [
            'column_id' => $card->column_id,
            'priority'  => $card->priority,
        ]);

        return response()->json(['message' => 'Kartu berhasil diperbarui.']);
    }

    public function destroyCard(int $cardId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.delete', true)) {
            return $denied;
        }

        $card = TaskCard::findOrFail($cardId);
        $columnId = $card->column_id;

        $this->logActivity($card->id, 'card_deleted', 'Menghapus kartu tugas.', [
            'title' => $card->title,
        ]);

        $card->delete();
        $this->reorderCardsInColumn($columnId);

        return response()->json(['message' => 'Kartu berhasil dihapus.']);
    }

    public function moveCard(Request $request, int $cardId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.update', true)) {
            return $denied;
        }

        $validated = $request->validate([
            'column_id' => 'required|integer|exists:task_columns,id',
            'position'  => 'required|integer|min:1',
        ]);

        $card = TaskCard::findOrFail($cardId);
        $fromColumnId = (int) $card->column_id;
        $toColumnId = (int) $validated['column_id'];
        $targetPosition = (int) $validated['position'];

        $targetColumn = TaskColumn::where('board_id', $card->board_id)->findOrFail($toColumnId);
        unset($targetColumn);

        DB::transaction(function () use ($card, $fromColumnId, $toColumnId, $targetPosition) {
            $card->column_id = $toColumnId;
            $card->updated_by = Auth::id();
            $card->save();

            $cards = TaskCard::where('column_id', $toColumnId)->orderBy('position')->pluck('id')->toArray();
            $cards = array_values(array_filter($cards, function ($id) use ($card) {
                return (int) $id !== (int) $card->id;
            }));

            $insertIndex = max(0, min(count($cards), $targetPosition - 1));
            array_splice($cards, $insertIndex, 0, [$card->id]);

            foreach ($cards as $index => $id) {
                TaskCard::where('id', $id)->update(['position' => $index + 1]);
            }

            if ($fromColumnId !== $toColumnId) {
                $this->reorderCardsInColumn($fromColumnId);
            }
        });

        $this->logActivity($card->id, 'card_moved', 'Memindahkan kartu antar kolom.', [
            'from_column_id' => $fromColumnId,
            'to_column_id'   => $toColumnId,
            'position'       => $targetPosition,
        ]);

        return response()->json(['message' => 'Kartu berhasil dipindahkan.']);
    }

    public function storeChecklist(Request $request, int $cardId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.update', true)) {
            return $denied;
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $card = TaskCard::findOrFail($cardId);
        $nextSort = (int) TaskChecklistItem::where('card_id', $card->id)->max('sort_order') + 1;

        $item = TaskChecklistItem::create([
            'card_id'    => $card->id,
            'title'      => $validated['title'],
            'is_done'    => false,
            'sort_order' => $nextSort,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $this->logActivity($card->id, 'checklist_added', 'Menambahkan checklist.', [
            'title' => $item->title,
        ]);

        return response()->json(['message' => 'Checklist berhasil ditambahkan.', 'item' => $item]);
    }

    public function toggleChecklist(Request $request, int $itemId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.update', true)) {
            return $denied;
        }

        $validated = $request->validate([
            'is_done' => 'required|boolean',
        ]);

        $item = TaskChecklistItem::findOrFail($itemId);
        $item->is_done = (bool) $validated['is_done'];
        $item->updated_by = Auth::id();
        $item->save();

        $this->logActivity($item->card_id, 'checklist_toggled', 'Mengubah status checklist.', [
            'item_id'  => $item->id,
            'is_done'  => $item->is_done,
            'title'    => $item->title,
        ]);

        return response()->json(['message' => 'Checklist diperbarui.']);
    }

    public function destroyChecklist(int $itemId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.update', true)) {
            return $denied;
        }

        $item = TaskChecklistItem::findOrFail($itemId);
        $cardId = $item->card_id;

        $this->logActivity($cardId, 'checklist_deleted', 'Menghapus checklist.', [
            'title' => $item->title,
        ]);

        $item->delete();

        return response()->json(['message' => 'Checklist dihapus.']);
    }

    public function storeComment(Request $request, int $cardId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.update', true)) {
            return $denied;
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:4000',
        ]);

        $card = TaskCard::findOrFail($cardId);

        $comment = TaskComment::create([
            'card_id' => $card->id,
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
        ]);

        $comment->load('user:id_user,nm_user');

        $this->logActivity($card->id, 'comment_added', 'Menambahkan komentar.', [
            'comment_id' => $comment->id,
        ]);

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan.',
            'comment' => $comment,
        ]);
    }

    public function destroyComment(int $commentId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.delete', true)) {
            return $denied;
        }

        $comment = TaskComment::findOrFail($commentId);
        $cardId = $comment->card_id;

        $this->logActivity($cardId, 'comment_deleted', 'Menghapus komentar.', [
            'comment_id' => $comment->id,
        ]);

        $comment->delete();

        return response()->json(['message' => 'Komentar dihapus.']);
    }

    public function storeAttachment(Request $request, int $cardId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.update', true)) {
            return $denied;
        }

        $validated = $request->validate([
            'attachment' => 'required|file|max:10240',
        ]);

        $card = TaskCard::findOrFail($cardId);
        $file = $validated['attachment'];
        $storedPath = $file->store('task-attachments', 'public');

        $attachment = TaskCardAttachment::create([
            'card_id'       => $card->id,
            'original_name' => $file->getClientOriginalName(),
            'file_name'     => basename($storedPath),
            'file_path'     => $storedPath,
            'mime_type'     => $file->getClientMimeType(),
            'file_size'     => $file->getSize(),
            'created_by'    => Auth::id(),
            'updated_by'    => Auth::id(),
        ]);

        $attachment->load('creator:id_user,nm_user');

        $this->logActivity($card->id, 'attachment_added', 'Menambahkan lampiran.', [
            'attachment_id' => $attachment->id,
            'name'          => $attachment->original_name,
        ]);

        return response()->json([
            'message'    => 'Lampiran berhasil ditambahkan.',
            'attachment' => $this->serializeAttachment($attachment),
        ]);
    }

    public function destroyAttachment(int $attachmentId)
    {
        if ($denied = $this->denyIfNoAccess('list-tugas.update', true)) {
            return $denied;
        }

        $attachment = TaskCardAttachment::findOrFail($attachmentId);
        $cardId = $attachment->card_id;

        if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $this->logActivity($cardId, 'attachment_deleted', 'Menghapus lampiran.', [
            'attachment_id' => $attachment->id,
            'name'          => $attachment->original_name,
        ]);

        $attachment->delete();

        return response()->json(['message' => 'Lampiran dihapus.']);
    }

    private function ensureDefaultBoard(): void
    {
        $board = TaskBoard::first();

        if ($board) {
            if ($board->columns()->count() === 0) {
                $defaultColumns = ['Backlog', 'To Do', 'In Progress', 'Done'];

                foreach ($defaultColumns as $index => $columnName) {
                    TaskColumn::create([
                        'board_id'   => $board->id,
                        'name'       => $columnName,
                        'sort_order' => $index + 1,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            return;
        }

        $board = TaskBoard::create([
            'name'       => 'Board Utama',
            'description'=> 'Board default untuk manajemen tugas.',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $defaultColumns = ['Backlog', 'To Do', 'In Progress', 'Done'];

        foreach ($defaultColumns as $index => $columnName) {
            TaskColumn::create([
                'board_id'   => $board->id,
                'name'       => $columnName,
                'sort_order' => $index + 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
    }

    private function normalizeLabels(?string $labels): ?string
    {
        if (!$labels) {
            return null;
        }

        $normalized = collect(explode(',', $labels))
            ->map(function ($item) {
                return trim($item);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (count($normalized) === 0) {
            return null;
        }

        return implode(', ', $normalized);
    }

    private function logActivity(?int $cardId, string $action, string $description, array $meta = []): void
    {
        TaskActivity::create([
            'card_id'     => $cardId,
            'user_id'     => Auth::id(),
            'action'      => $action,
            'description' => $description,
            'meta'        => $meta ?: null,
        ]);
    }

    private function reorderCardsInColumn(int $columnId): void
    {
        $cards = TaskCard::where('column_id', $columnId)->orderBy('position')->get(['id']);

        foreach ($cards as $index => $card) {
            TaskCard::where('id', $card->id)->update(['position' => $index + 1]);
        }
    }

    private function reorderColumns(int $boardId): void
    {
        $columns = TaskColumn::where('board_id', $boardId)->orderBy('sort_order')->get(['id']);

        foreach ($columns as $index => $column) {
            TaskColumn::where('id', $column->id)->update(['sort_order' => $index + 1]);
        }
    }

    private function denyIfNoAccess(string $permission, bool $json = false)
    {
        if ($this->hasPermissionAccess($permission)) {
            return null;
        }

        if ($json) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return redirect(url()->previous() ?: 'dashboard')
            ->with('error', "Anda tidak memiliki izin untuk mengakses {$permission}. Hubungi Admin untuk Aktivasi Kembali");
    }

    private function hasPermissionAccess(string $permission): bool
    {
        try {
            return $this->hasAccess($permission);
        } catch (Throwable $exception) {
            $userId = Auth::id();
            if ($userId) {
                $roleId = DB::table('role_user')->where('id_user', $userId)->value('id_role');
                if ($roleId) {
                    if ((int) $roleId === 1) {
                        return true;
                    }

                    $hasRoleMenuAccess = DB::table('role_menu')
                        ->where('id_role', $roleId)
                        ->where('permission', $permission)
                        ->exists();

                    if ($hasRoleMenuAccess) {
                        return true;
                    }
                }
            }

            $user = Auth::user();
            if (!$user || !method_exists($user, 'can')) {
                return false;
            }

            try {
                return (bool) $user->can($permission);
            } catch (Throwable $innerException) {
                return false;
            }
        }
    }

    private function serializeAttachment(TaskCardAttachment $attachment): array
    {
        return [
            'id'             => $attachment->id,
            'card_id'        => $attachment->card_id,
            'original_name'  => $attachment->original_name,
            'file_name'      => $attachment->file_name,
            'file_path'      => $attachment->file_path,
            'mime_type'      => $attachment->mime_type,
            'file_size'      => (int) $attachment->file_size,
            'file_size_text' => $this->formatFileSize((int) $attachment->file_size),
            'file_url'       => '/storage/' . ltrim($attachment->file_path, '/'),
            'created_at'     => optional($attachment->created_at)->toISOString(),
            'creator'        => $attachment->creator ? [
                'id_user' => $attachment->creator->id_user,
                'nm_user' => $attachment->creator->nm_user,
            ] : null,
        ];
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return round($bytes / (1024 * 1024), 1) . ' MB';
    }
}
