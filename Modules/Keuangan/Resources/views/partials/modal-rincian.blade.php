<!-- Modal Drill-Down Rincian Transaksi Akun (Zero N+1) -->
<div id="modal-rincian-akun" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div id="modal-rincian-backdrop" onclick="closeRincianModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Dialog -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border border-slate-200">
            <!-- Modal Header -->
            <div class="bg-slate-900 px-6 py-4 flex items-center justify-between text-white border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-500/30 flex items-center justify-center">
                        <i data-lucide="layers" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-blue-500/30 text-blue-300 border border-blue-500/40" id="modal-akun-kode">-</span>
                            <h3 class="text-base font-bold text-white tracking-tight" id="modal-akun-nama">Rincian Transaksi Akun</h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5" id="modal-akun-meta">Memuat informasi akun...</p>
                    </div>
                </div>
                <button type="button" onclick="closeRincianModal()" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 bg-slate-50/50">
                <!-- Summary Mini Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Saldo Awal</span>
                        <span class="text-sm font-bold text-slate-800 font-mono mt-1 block" id="modal-stat-saldo-awal">Rp 0</span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-blue-100 shadow-xs">
                        <span class="text-[10px] font-bold text-blue-500 uppercase tracking-wider block">Total Mutasi Debit</span>
                        <span class="text-sm font-bold text-blue-700 font-mono mt-1 block" id="modal-stat-total-debit">Rp 0</span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-indigo-100 shadow-xs">
                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider block">Total Mutasi Kredit</span>
                        <span class="text-sm font-bold text-indigo-700 font-mono mt-1 block" id="modal-stat-total-kredit">Rp 0</span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-emerald-100 shadow-xs">
                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Saldo Akhir</span>
                        <span class="text-sm font-black text-emerald-700 font-mono mt-1 block" id="modal-stat-saldo-akhir">Rp 0</span>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="list" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Histori Jurnal &amp; Mutasi Periode</span>
                        </div>
                        <span class="text-xs font-bold text-slate-500" id="modal-entry-count">0 Transaksi</span>
                    </div>

                    <div class="overflow-x-auto max-h-[380px] overflow-y-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-100 text-slate-700 uppercase text-[10px] font-bold tracking-wider sticky top-0 z-10 border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-3 w-10 text-center">No</th>
                                    <th class="py-2.5 px-3 w-24">Tanggal</th>
                                    <th class="py-2.5 px-3">No. Transaksi</th>
                                    <th class="py-2.5 px-3">Keterangan &amp; Kontak</th>
                                    <th class="py-2.5 px-3">Lawan Akun</th>
                                    <th class="py-2.5 px-3 text-right">Debit (Rp)</th>
                                    <th class="py-2.5 px-3 text-right">Kredit (Rp)</th>
                                    <th class="py-2.5 px-3 text-right">Saldo Berjalan</th>
                                </tr>
                            </thead>
                            <tbody id="modal-rincian-tbody" class="divide-y divide-slate-100 font-sans">
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                                            <span>Memuat data transaksi...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-white px-6 py-3 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-blue-500"></i>
                    <span>Data dihitung secara satu pintu langsung dari Model Jurnal Umum (Double-Entry).</span>
                </div>
                <div class="flex items-center gap-2">
                    <a id="modal-btn-open-jurnal" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        Buka di Jurnal Umum
                    </a>
                    <button type="button" onclick="closeRincianModal()" class="px-4 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function formatRupiah(num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    function closeRincianModal() {
        document.getElementById('modal-rincian-akun').classList.add('hidden');
    }

    function openRincianModal(coaId, endpointBaseUrl) {
        const modal = document.getElementById('modal-rincian-akun');
        modal.classList.remove('hidden');

        // Reset to loading state
        document.getElementById('modal-akun-kode').innerText = '...';
        document.getElementById('modal-akun-nama').innerText = 'Memuat Transaksi...';
        document.getElementById('modal-akun-meta').innerText = 'Mengambil mutasi dari database...';
        document.getElementById('modal-stat-saldo-awal').innerText = '...';
        document.getElementById('modal-stat-total-debit').innerText = '...';
        document.getElementById('modal-stat-total-kredit').innerText = '...';
        document.getElementById('modal-stat-saldo-akhir').innerText = '...';
        document.getElementById('modal-entry-count').innerText = 'Memuat...';

        const tbody = document.getElementById('modal-rincian-tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-10 text-slate-400">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div class="w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                        <span>Memuat rincian transaksi akun...</span>
                    </div>
                </td>
            </tr>
        `;

        const startDate = document.getElementById('filter-start-date')?.value || '';
        const endDate = document.getElementById('filter-end-date')?.value || '';

        const url = `${endpointBaseUrl}/${coaId}?start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Gagal memuat rincian akun');
            return response.json();
        })
        .then(res => {
            const data = res.data;
            const account = data.account;

            document.getElementById('modal-akun-kode').innerText = account.kode_akun || '-';
            document.getElementById('modal-akun-nama').innerText = account.nama_akun || 'Akun';
            document.getElementById('modal-akun-meta').innerText = `Kategori: ${account.kategori || '-'} • Posisi Normal: ${data.posisi_normal} • Periode: ${startDate || 'Awal'} s/d ${endDate || 'Sekarang'}`;

            document.getElementById('modal-stat-saldo-awal').innerText = formatRupiah(data.saldo_awal);
            document.getElementById('modal-stat-total-debit').innerText = formatRupiah(data.total_debit);
            document.getElementById('modal-stat-total-kredit').innerText = formatRupiah(data.total_kredit);
            document.getElementById('modal-stat-saldo-akhir').innerText = formatRupiah(data.saldo_akhir);
            document.getElementById('modal-entry-count').innerText = `${data.total_transaksi} Transaksi`;

            // Setup deep link ke Jurnal Umum
            const jurnalUrl = `{{ route('jurnal.index') }}?coa_id=${coaId}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;
            document.getElementById('modal-btn-open-jurnal').href = jurnalUrl;

            if (data.entries.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-8 text-slate-400">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-1 text-slate-300"></i>
                            <p class="font-medium">Tidak ada transaksi pada periode ini.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Saldo awal tetap dipertahankan sebesar ${formatRupiah(data.saldo_awal)}</p>
                        </td>
                    </tr>
                `;
            } else {
                let html = '';
                data.entries.forEach((row, idx) => {
                    const isMasuk = (row.jenis === 'masuk');
                    const badgeClass = isMasuk
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        : 'bg-rose-50 text-rose-700 border-rose-200';

                    const debitText = row.debit > 0
                        ? `<span class="font-mono font-bold text-blue-700">${formatRupiah(row.debit)}</span>`
                        : `<span class="text-slate-300 font-mono">-</span>`;

                    const kreditText = row.kredit > 0
                        ? `<span class="font-mono font-bold text-indigo-700">${formatRupiah(row.kredit)}</span>`
                        : `<span class="text-slate-300 font-mono">-</span>`;

                    html += `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono">${idx + 1}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap text-slate-700 font-semibold">${row.tanggal}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold border ${badgeClass}">
                                    ${row.nomor_transaksi}
                                </span>
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="font-medium text-slate-900">${row.keterangan || '-'}</div>
                                ${row.penerima_pembayar ? `<div class="text-[11px] text-slate-500">Pihak: <strong>${row.penerima_pembayar}</strong></div>` : ''}
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <div class="text-slate-800 font-medium">${row.lawan_nama_akun || '-'}</div>
                                <div class="text-[10px] font-mono text-slate-400">${row.lawan_kode_akun || ''}</div>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">${debitText}</td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">${kreditText}</td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap font-mono font-bold text-slate-900">${formatRupiah(row.saldo_berjalan)}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }

            if (window.lucide) {
                lucide.createIcons();
            }
        })
        .catch(err => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-6 text-rose-500">
                        <p class="font-bold">Terjadi kesalahan memuat data.</p>
                        <p class="text-xs text-rose-400">${err.message}</p>
                    </td>
                </tr>
            `;
        });
    }
</script>
