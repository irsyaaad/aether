@extends('layouts.app')

@section('title', 'Master Metode Riset & Analisis - ' . config('app.name'))
@section('page_title', 'Master Metode Riset & Analisis')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 bg-emerald-50 text-emerald-700 rounded-xl">
                    <i data-lucide="microscope" class="w-5 h-5"></i>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Database Metode Analisis &amp; Software Statistik</h2>
                    <p class="text-xs text-gray-500">Kelola daftar metode penelitian, rekomendasi software (SPSS, SmartPLS, EViews, Stata), serta indikator ujinya.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Metode Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="w-full sm:w-64">
                <select id="filter-kategori" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                    <option value="">Semua Kategori Riset</option>
                    <option value="Kuantitatif">Kuantitatif</option>
                    <option value="Kualitatif">Kualitatif</option>
                    <option value="Ekonometrika">Ekonometrika</option>
                    <option value="Mixed Method">Mixed Method</option>
                </select>
            </div>
            <button type="button" onclick="refreshTable()" class="py-2.5 px-3 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-1.5 transition">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                <span>Refresh</span>
            </button>
        </div>

        <div class="overflow-x-auto pt-2">
            <table id="metode-table" class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50/80 text-[11px] uppercase tracking-wider text-gray-500 font-bold border-y border-gray-200">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Metode Riset</th>
                        <th class="py-3.5 px-4">Kategori Riset</th>
                        <th class="py-3.5 px-4">Software Rekomendasi</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Create / Edit Metode -->
<div id="modal-metode" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/50">
            <h3 id="modal-title" class="font-bold text-gray-900 text-sm flex items-center gap-2">
                <i data-lucide="microscope" class="w-4 h-4 text-[#1768B5]"></i>
                <span>Tambah Metode Riset</span>
            </h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="form-metode" onsubmit="submitForm(event)" class="p-6 space-y-4">
            <input type="hidden" id="metode_id" name="id">
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Metode <span class="text-rose-500">*</span></label>
                    <input type="text" id="kode_metode" name="kode_metode" required placeholder="Contoh: MET-PLS" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 font-mono uppercase focus:ring-2 focus:ring-[#1768B5]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori Riset <span class="text-rose-500">*</span></label>
                    <select id="kategori" name="kategori" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                        <option value="Kuantitatif">Kuantitatif</option>
                        <option value="Kualitatif">Kualitatif</option>
                        <option value="Ekonometrika">Ekonometrika</option>
                        <option value="Mixed Method">Mixed Method</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Metode <span class="text-rose-500">*</span></label>
                <input type="text" id="nama_metode" name="nama_metode" required placeholder="Contoh: Partial Least Squares Structural Equation Modeling" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Software Rekomendasi</label>
                <input type="text" id="software_rekomendasi" name="software_rekomendasi" placeholder="Contoh: SmartPLS 4, SPSS, EViews, Stata" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Daftar Uji Statistik / Output Terkait</label>
                <input type="text" id="uji_statistik_terkait" name="uji_statistik_terkait" placeholder="Contoh: AVE, HTMT, R-Square, Path Analysis, Uji Chow..." class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi &amp; Cakupan Metodologi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan asumsi metode dan kondisi pemakaiannya..." class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Batal</button>
                <button type="submit" id="btn-save" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-sm transition">Simpan Metode</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var table;
    $(document).ready(function() {
        table = $('#metode-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("master.metode.get-data") }}',
                data: function(d) {
                    d.kategori = $('#filter-kategori').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-bold text-gray-400' },
                { data: 'metode_info', name: 'nama_metode' },
                { data: 'kategori_badge', name: 'kategori' },
                { data: 'software_badge', name: 'software_rekomendasi' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            drawCallback: function() {
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        });

        $('#filter-kategori').on('change', function() {
            table.draw();
        });
    });

    function refreshTable() {
        $('#filter-kategori').val('');
        table.draw();
    }

    function openCreateModal() {
        $('#modal-title span').text('Tambah Metode Riset');
        $('#form-metode')[0].reset();
        $('#metode_id').val('');
        $('#modal-metode').removeClass('hidden');
        if (window.lucide) { lucide.createIcons(); }
    }

    function closeModal() {
        $('#modal-metode').addClass('hidden');
    }

    function editMetode(row) {
        $('#modal-title span').text('Edit Metode: ' + row.kode_metode);
        $('#metode_id').val(row.id);
        $('#kode_metode').val(row.kode_metode);
        $('#nama_metode').val(row.nama_metode);
        $('#kategori').val(row.kategori);
        $('#software_rekomendasi').val(row.software_rekomendasi);
        $('#uji_statistik_terkait').val(row.uji_statistik_terkait);
        $('#deskripsi').val(row.deskripsi);
        $('#modal-metode').removeClass('hidden');
        if (window.lucide) { lucide.createIcons(); }
    }

    function submitForm(e) {
        e.preventDefault();
        let id = $('#metode_id').val();
        let isEdit = id ? true : false;
        let url = isEdit ? '{{ url("master/metode") }}/' + id : '{{ route("master.metode.store") }}';
        let method = isEdit ? 'PUT' : 'POST';

        let formData = $('#form-metode').serialize();
        if (isEdit) {
            formData += '&_method=PUT';
        }

        $('#btn-save').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(res) {
                $('#btn-save').prop('disabled', false).text('Simpan Metode');
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                table.draw(false);
            },
            error: function(err) {
                $('#btn-save').prop('disabled', false).text('Simpan Metode');
                let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Terjadi kesalahan sistem';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: msg
                });
            }
        });
    }

    function deleteMetode(id, name) {
        Swal.fire({
            title: 'Hapus Metode Riset?',
            text: 'Metode "' + name + '" akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("master/metode") }}/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        table.draw(false);
                    },
                    error: function(err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal menghapus data.'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
