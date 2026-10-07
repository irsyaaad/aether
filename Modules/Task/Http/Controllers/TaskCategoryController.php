<?php

namespace Modules\Task\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Master\Entities\Company;
use Modules\Task\Entities\TaskCategory;
use Yajra\DataTables\Facades\DataTables;

class TaskCategoryController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of categories
     */
    public function index()
    {
        if ($denied = $this->requireAccess('task_categories.index')) {
            return $denied;
        }

        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('task::categories.index', compact('companies'));
    }

    /**
     * Server-side DataTables
     */
    public function getData(Request $request)
    {
        if ($denied = $this->requireAccess('task_categories.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = TaskCategory::with(['company', 'creator'])->withCount('tasks');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1' || $request->status === 'active');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('name', function ($row) {
                return '<div class="flex flex-col">
                            <span class="font-medium text-gray-900 text-sm">' . e($row->name) . '</span>
                            <span class="text-xs text-gray-500 font-mono mt-0.5">' . e($row->code) . '</span>
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
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? '<span class="text-gray-600 text-xs">' . $row->created_at->format('d M Y H:i') . '</span>' : '-';
            })
            ->addColumn('action', function ($row) {
                $jsonData = htmlspecialchars(json_encode([
                    'id'          => $row->id,
                    'company_id'  => $row->company_id,
                    'name'        => $row->name,
                    'code'        => $row->code,
                    'description' => $row->description,
                    'is_active'   => $row->is_active ? 1 : 0,
                ]), ENT_QUOTES, 'UTF-8');

                return '
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" class="p-1.5 text-gray-400 hover:text-primary transition-colors rounded-full hover:bg-primary/10" title="Edit" onclick="editCategory(' . $jsonData . ')">
                            <i data-lucide="edit-3" class="h-4 w-4"></i>
                        </button>
                        <button type="button" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors rounded-full hover:bg-red-50" title="Hapus" onclick="deleteCategory(' . $row->id . ', \'' . e(addslashes($row->name)) . '\')">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['name', 'company_name', 'tasks_count', 'is_active', 'created_at', 'action'])
            ->make(true);
    }

    /**
     * Store category
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('task_categories.create')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'name'        => 'required|string|max:255',
            'code'        => [
                'required',
                'string',
                'max:50',
                Rule::unique('task_categories')->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id)->whereNull('deleted_at');
                }),
            ],
            'description' => 'nullable|string',
            'is_active'   => 'required|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            $category = TaskCategory::create([
                'company_id'  => $request->company_id,
                'name'        => $request->name,
                'code'        => strtoupper(trim($request->code)),
                'description' => $request->description,
                'is_active'   => (bool) $request->is_active,
                'created_by'  => Auth::user()->id_user ?? Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Kategori berhasil ditambahkan',
                'data'    => $category,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan kategori: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update category
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('task_categories.update')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $category = TaskCategory::findOrFail($id);

        $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'name'        => 'required|string|max:255',
            'code'        => [
                'required',
                'string',
                'max:50',
                Rule::unique('task_categories')->ignore($category->id)->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id)->whereNull('deleted_at');
                }),
            ],
            'description' => 'nullable|string',
            'is_active'   => 'required|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            $category->update([
                'company_id'  => $request->company_id,
                'name'        => $request->name,
                'code'        => strtoupper(trim($request->code)),
                'description' => $request->description,
                'is_active'   => (bool) $request->is_active,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Kategori berhasil diupdate',
                'data'    => $category,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengupdate kategori: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete category
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('task_categories.delete')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();
            $category = TaskCategory::findOrFail($id);

            if ($category->tasks()->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Kategori tidak dapat dihapus karena sedang digunakan oleh tugas aktif.',
                ], 422);
            }

            $category->delete();
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Kategori berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus kategori: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active categories by company ID for cascading dropdowns
     */
    public function byCompany($companyId)
    {
        $categories = TaskCategory::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($categories);
    }
}
