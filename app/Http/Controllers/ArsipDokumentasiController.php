<?php
namespace App\Http\Controllers;

use App\Models\ArsipDokumentasi;
use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ArsipDokumentasiController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($denied = $this->requireAccess('arsip-dokumentasi.index')) {
            return $denied;
        }

        $data['filter'] = [
            'dr_tgl' => date('Y-m-d', strtotime('-30 days')),
            'sp_tgl' => date('Y-m-d'),
        ];

        return view('arsip-dokumentasi.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($denied = $this->requireAccess('arsip-dokumentasi.create')) {
            return $denied;
        }

        return view('arsip-dokumentasi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('arsip-dokumentasi.create')) {
            return $denied;
        }

        $request->validate([
            'judul'               => 'required|string|max:255',
            'deskripsi'           => 'nullable|string',
            'tipe_file'           => 'required|in:foto,video',
            'file'                => 'required|array|min:1',
            'file.*'              => 'required|file|max:51200', // 50MB max
            'kategori'            => 'required|in:kegiatan,fasilitas,karyawan,produk,acara,lainnya',
            'tanggal_dokumentasi' => 'nullable|date',
            'lokasi'              => 'nullable|string|max:255',
            'tags'                => 'nullable|string',
            'is_public'           => 'boolean',
        ]);

        // Validate file type based on tipe_file
        if ($request->tipe_file === 'foto') {
            $request->validate([
                'file.*' => 'mimes:jpeg,jpg,png,gif,webp|max:10240', // 10MB for images
            ]);
        } else {
            $request->validate([
                'file.*' => 'mimes:mp4,avi,mov,wmv,flv,webm|max:51200', // 50MB for videos
            ]);
        }

        $uploadedPaths = [];
        $names         = [];
        $paths         = [];
        $sizes         = [];
        $formats       = [];

        try {
            DB::beginTransaction();

            foreach ($request->file('file') as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $extension    = $file->getClientOriginalExtension();
                $size         = $file->getSize();

                // Generate unique filename
                $filename = time() . '_' . $index . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

                // Store file in appropriate directory
                $directory = $request->tipe_file === 'foto' ? 'arsip/foto' : 'arsip/video';
                $path      = $file->storeAs($directory, $filename, 'public');

                $uploadedPaths[] = $path;
                $names[]         = $originalName;
                $paths[]         = $path;
                $sizes[]         = $size;
                $formats[]       = $extension;
            }

            // Process tags
            $tags = null;
            if ($request->tags) {
                $tags = array_map('trim', explode(',', $request->tags));
            }

            ArsipDokumentasi::create([
                'judul'               => $request->judul,
                'deskripsi'           => $request->deskripsi,
                'tipe_file'           => $request->tipe_file,
                'nama_file'           => $names,
                'path_file'           => $paths,
                'ukuran_file'         => $sizes,
                'format_file'         => $formats,
                'kategori'            => $request->kategori,
                'tanggal_dokumentasi' => $request->tanggal_dokumentasi,
                'lokasi'              => $request->lokasi,
                'tags'                => $tags,
                'is_public'           => $request->boolean('is_public'),
                'status'              => 'aktif',
            ]);

            DB::commit();
            return redirect()->route('arsip-dokumentasi.index')->with('success', 'Arsip dokumentasi berhasil diupload.');

        } catch (\Throwable $th) {
            DB::rollBack();

            // Delete uploaded files if error occurs
            foreach ($uploadedPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return redirect()->back()->withInput()->with('error', 'Gagal mengupload arsip: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('arsip-dokumentasi.index')) {
            return $denied;
        }

        $arsip = ArsipDokumentasi::with(['uploader', 'updater'])->findOrFail($id);

        return view('arsip-dokumentasi.show', compact('arsip'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('arsip-dokumentasi.update')) {
            return $denied;
        }

        $arsip = ArsipDokumentasi::findOrFail($id);

        return view('arsip-dokumentasi.edit', compact('arsip'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('arsip-dokumentasi.update')) {
            return $denied;
        }

        $arsip = ArsipDokumentasi::findOrFail($id);

        $request->validate([
            'judul'               => 'required|string|max:255',
            'deskripsi'           => 'nullable|string',
            'kategori'            => 'required|in:kegiatan,fasilitas,karyawan,produk,acara,lainnya',
            'tanggal_dokumentasi' => 'nullable|date',
            'lokasi'              => 'nullable|string|max:255',
            'tags'                => 'nullable|string',
            'status'              => 'required|in:aktif,arsip,dihapus',
            'is_public'           => 'boolean',
            'file'                => 'nullable|array',
            'file.*'              => 'file|max:51200', // 50MB max per file
            'deleted_files'       => 'nullable|array',
            'deleted_files.*'     => 'integer|min:0',
        ]);

        // Validate file type based on existing tipe_file
        if ($arsip->tipe_file === 'foto') {
            $request->validate([
                'file.*' => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:10240', // 10MB for images
            ]);
        } else {
            $request->validate([
                'file.*' => 'nullable|mimes:mp4,avi,mov,wmv,flv,webm|max:51200', // 50MB for videos
            ]);
        }

        try {
            DB::beginTransaction();

            // Get current file arrays
            $currentNames   = (array) $arsip->nama_file;
            $currentPaths   = (array) $arsip->path_file;
            $currentSizes   = (array) $arsip->ukuran_file;
            $currentFormats = (array) $arsip->format_file;

            // Handle deleted files - remove by index
            $deletedFiles = $request->deleted_files ?? [];
            if (! empty($deletedFiles)) {
                // Sort indices in descending order to avoid index shifting issues
                $indicesToDelete = array_unique($deletedFiles);
                rsort($indicesToDelete);

                foreach ($indicesToDelete as $index) {
                    if (isset($currentPaths[$index])) {
                        // Delete physical file
                        if (Storage::disk('public')->exists($currentPaths[$index])) {
                            Storage::disk('public')->delete($currentPaths[$index]);
                        }

                        // Remove from arrays
                        unset($currentNames[$index]);
                        unset($currentPaths[$index]);
                        unset($currentSizes[$index]);
                        unset($currentFormats[$index]);
                    }
                }

                // Re-index arrays
                $currentNames   = array_values($currentNames);
                $currentPaths   = array_values($currentPaths);
                $currentSizes   = array_values($currentSizes);
                $currentFormats = array_values($currentFormats);
            }

            // Handle new file uploads
            $uploadedPaths = [];
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $index => $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension    = $file->getClientOriginalExtension();
                    $size         = $file->getSize();

                    // Generate unique filename
                    $filename = time() . '_' . $index . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

                    // Store file in appropriate directory
                    $directory = $arsip->tipe_file === 'foto' ? 'arsip/foto' : 'arsip/video';
                    $path      = $file->storeAs($directory, $filename, 'public');

                    $currentPaths[]   = $path;
                    $currentNames[]   = $originalName;
                    $currentSizes[]   = $size;
                    $currentFormats[] = $extension;

                    $uploadedPaths[] = $path;
                }
            }

            // Check if there are still files remaining
            if (empty($currentPaths) && empty($uploadedPaths)) {
                throw new \Exception('Dokumentasi harus memiliki setidaknya satu file.');
            }

            // Process tags
            $tags = null;
            if ($request->tags) {
                $tags = array_map('trim', explode(',', $request->tags));
            }

            $arsip->update([
                'judul'               => $request->judul,
                'deskripsi'           => $request->deskripsi,
                'kategori'            => $request->kategori,
                'tanggal_dokumentasi' => $request->tanggal_dokumentasi,
                'lokasi'              => $request->lokasi,
                'tags'                => $tags,
                'status'              => $request->status,
                'is_public'           => $request->boolean('is_public'),
                'nama_file'           => $currentNames,
                'path_file'           => $currentPaths,
                'ukuran_file'         => $currentSizes,
                'format_file'         => $currentFormats,
            ]);

            DB::commit();
            return redirect()->route('arsip-dokumentasi.index')->with('success', 'Arsip dokumentasi berhasil diperbarui.');

        } catch (\Throwable $th) {
            DB::rollBack();

            // Delete uploaded files if error occurs (they were already stored)
            foreach ($uploadedPaths ?? [] as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui arsip: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('arsip-dokumentasi.delete')) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);
        }

        try {
            DB::beginTransaction();

            $arsip = ArsipDokumentasi::findOrFail($id);

            // Delete physical files
            foreach ((array) $arsip->path_file as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            $arsip->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Arsip berhasil dihapus.']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus arsip: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = ArsipDokumentasi::with(['uploader'])
            ->when($request->filter_tipe, function ($q) use ($request) {
                return $q->where('tipe_file', $request->filter_tipe);
            })
            ->when($request->filter_kategori, function ($q) use ($request) {
                return $q->where('kategori', $request->filter_kategori);
            })
            ->when($request->filter_status, function ($q) use ($request) {
                return $q->where('status', $request->filter_status);
            })
            ->when($request->dr_tgl && $request->sp_tgl, function ($q) use ($request) {
                return $q->whereBetween('tanggal_dokumentasi', [$request->dr_tgl, $request->sp_tgl]);
            })
            ->latest();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('preview', function ($row) {
                if ($row->isImage()) {
                    $fileUrl = $row->file_url;
                    if ($fileUrl) {
                        return '<img src="' . $fileUrl . '" alt="' . htmlspecialchars($row->judul) . '" class="img-thumbnail" style="max-width: 60px; max-height: 60px; object-fit: cover;" onerror="this.src=\'/images/placeholder.png\'">';
                    } else {
                        return '<img src="/images/placeholder.png" alt="No Image" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">';
                    }
                } else {
                    return '<i class="fa fa-video fa-2x text-primary"></i>';
                }
            })
            ->editColumn('judul', function ($row) {
                return '<strong>' . htmlspecialchars($row->judul) . '</strong><br><small class="text-muted">' . Str::limit($row->deskripsi, 50) . '</small>';
            })
            ->addColumn('file_info', function ($row) {
                $formats = (array) $row->format_file;
                $ext     = ! empty($formats) ? strtoupper($formats[0]) : '-';
                if (count($formats) > 1) {
                    $ext .= ' (+' . (count($formats) - 1) . ')';
                }
                return '<span class="badge bg-info">' . $ext . '</span><br><small>' . $row->formatted_size . '</small>';
            })
            ->editColumn('kategori', function ($row) {
                return '<span class="badge ' . $row->getCategoryBadgeClass() . '">' . ucfirst($row->kategori) . '</span>';
            })
            ->editColumn('status', function ($row) {
                return '<span class="badge ' . $row->getStatusBadgeClass() . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('uploader', function ($row) {
                return $row->uploader->nm_user ?? '-';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d-m-Y H:i');
            })
            ->addColumn('action', function ($row) {
                $show = route('arsip-dokumentasi.show', $row->id);
                $edit = route('arsip-dokumentasi.edit', $row->id);

                return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="' . $show . '"><i class="fa fa-eye text-primary"></i> Lihat</a></li>
                            <li><a class="dropdown-item" href="' . $edit . '"><i class="fa fa-edit text-warning"></i> Edit</a></li>
                            <li><button class="dropdown-item text-danger btn-delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i> Hapus</button></li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['preview', 'judul', 'file_info', 'kategori', 'status', 'action'])
            ->make(true);
    }

    /**
     * Download file
     */
    public function download($id)
    {
        if ($denied = $this->requireAccess('arsip-dokumentasi.index')) {
            return $denied;
        }

        $arsip = ArsipDokumentasi::findOrFail($id);

        $paths = (array) $arsip->path_file;
        $names = (array) $arsip->nama_file;

        if (empty($paths) || ! Storage::disk('public')->exists($paths[0])) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // For now, download the first file. If multiple, user should download from detail page.
        return Storage::disk('public')->download($paths[0], $names[0] ?? 'file');
    }

    /**
     * Debug method to check file URLs
     */
    public function debug()
    {
        $arsips = ArsipDokumentasi::latest()->take(5)->get();

        $debug = [];
        foreach ($arsips as $arsip) {
            $debug[] = [
                'id'           => $arsip->id,
                'judul'        => $arsip->judul,
                'path_file'    => $arsip->path_file,
                'file_url'     => $arsip->file_url,
                'file_exists'  => Storage::disk('public')->exists($arsip->path_file),
                'storage_path' => storage_path('app/public/' . $arsip->path_file),
                'public_path'  => public_path('storage/' . $arsip->path_file),
            ];
        }

        return response()->json($debug);
    }
}
