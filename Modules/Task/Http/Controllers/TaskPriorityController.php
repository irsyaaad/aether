<?php

namespace Modules\Task\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Master\Entities\Company;
use Modules\Task\Entities\TaskPriority;
use Yajra\DataTables\Facades\DataTables;

class TaskPriorityController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of priorities
     */
    public function index()
    {
        if ($denied = $this->requireAccess('task_priorities.index')) {
            return $denied;
        }

        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('task::priorities.index', compact('companies'));
    }

    /**
     * Server-side DataTables
     */
    public function getData(Request $request)
    {
        if ($denied = $this->requireAccess('task_priorities.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = TaskPriority::with('company')->withCount('tasks');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1' || $request->status === 'active');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('name', function ($row) {
                return '<div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border ' . $row->badge_class . '">
                                ' . e($row->name) . '
                            </span>
                            <span class="text-xs text-gray-500 font-mono">(' . e($row->code) . ')</span>
                        </div>';
            })
            ->addColumn('company_name', function ($row) {
                if (!$row->company) {
                    return '<span class="text-gray-400">-</span>';
                }
                return '<div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-primary shrink-0"></div>
                            <span class="text-xs font-medium text-gray-800">' . e($row->company->name) . '</span>
                        </div>';
            })
            ->editColumn('level', function ($row) {
                return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            Level ' . $row->level . '
                        </span>';
            })
            ->addColumn('tasks_count', function ($row) {
                return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                            ' . $row->tasks_count . ' Tugas
                        </span>';
            })
            ->editColumn('is_active', function ($row) {
                if ($row->is_active) {
                    return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border-emerald-200">Aktif</span>';
                }
                return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-red-50 text-red-700 border-red-200">Nonaktif</span>';
            })
            ->addColumn('action', function ($row) {
                $jsonData = htmlspecialchars(json_encode([
                    'id'          => $row->id,
                    'company_id'  => $row->company_id,
                    'name'        => $row->name,
                    'code'        => $row->code,
                    'level'       => $row->level,
                    'description' => $row->description,
                    'is_active'   => $row->is_active ? 1 : 0,
                ]), ENT_QUOTES, 'UTF-8');

                return '
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" class="p-1.5 text-gray-400 hover:text-primary transition-colors rounded-full hover:bg-primary/10" title="Edit" onclick="editPriority(' . $jsonData . ')">
                            <i data-lucide="edit-3" class="h-4 w-4"></i>
                        </button>
                        <button type="button" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors rounded-full hover:bg-red-50" title="Hapus" onclick="deletePriority(' . $row->id . ', \'' . e(addslashes($row->name)) . '\')">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['name', 'company_name', 'level', 'tasks_count', 'is_active', 'action'])
            ->make(true);
    }

    /**
     * Store priority
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('task_priorities.create')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'name'        => 'required|string|max:255',
            'code'        => [
                'required',
                'string',
                'max:50',
                Rule::unique('task_priorities')->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id)->whereNull('deleted_at');
                }),
            ],
            'level'       => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
            'is_active'   => 'required|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            $priority = TaskPriority::create([
                'company_id'  => $request->company_id,
                'name'        => $request->name,
                'code'        => strtoupper(trim($request->code)),
                'level'       => (int) $request->level,
                'description' => $request->description,
                'is_active'   => (bool) $request->is_active,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Prioritas berhasil ditambahkan',
                'data'    => $priority,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan prioritas: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update priority
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('task_priorities.update')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $priority = TaskPriority::findOrFail($id);

        $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'name'        => 'required|string|max:255',
            'code'        => [
                'required',
                'string',
                'max:50',
                Rule::unique('task_priorities')->ignore($priority->id)->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id)->whereNull('deleted_at');
                }),
            ],
            'level'       => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
            'is_active'   => 'required|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            $priority->update([
                'company_id'  => $request->company_id,
                'name'        => $request->name,
                'code'        => strtoupper(trim($request->code)),
                'level'       => (int) $request->level,
                'description' => $request->description,
                'is_active'   => (bool) $request->is_active,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Prioritas berhasil diupdate',
                'data'    => $priority,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengupdate prioritas: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete priority
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('task_priorities.delete')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();
            $priority = TaskPriority::findOrFail($id);

            if ($priority->tasks()->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Prioritas tidak dapat dihapus karena sedang digunakan oleh tugas aktif.',
                ], 422);
            }

            $priority->delete();
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Prioritas berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus prioritas: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active priorities by company ID for cascading dropdowns
     */
    public function byCompany($companyId)
    {
        $priorities = TaskPriority::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('level')
            ->get(['id', 'name', 'code', 'level']);

        return response()->json($priorities);
    }
}
