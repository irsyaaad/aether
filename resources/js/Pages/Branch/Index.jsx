import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ branches, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || '');
    const [sort, setSort] = useState(filters.sort || 'name_asc');

    useEffect(() => {
        const timer = setTimeout(() => {
            if (search !== (filters.search || '')) {
                router.get('/branches', { search, status, sort }, { preserveState: true, replace: true });
            }
        }, 400);

        return () => clearTimeout(timer);
    }, [search]);

    const handleFilterSubmit = (e) => {
        if (e && e.preventDefault) e.preventDefault();
        router.get('/branches', { search, status, sort }, { preserveState: true });
    };

    const handleStatusChange = (e) => {
        const newStatus = e.target.value;
        setStatus(newStatus);
        router.get('/branches', { search, status: newStatus, sort }, { preserveState: true });
    };

    const handleSortChange = (e) => {
        const newSort = e.target.value;
        setSort(newSort);
        router.get('/branches', { search, status, sort: newSort }, { preserveState: true });
    };

    const handleDelete = (id) => {
        if (typeof window !== 'undefined' && window.Swal) {
            window.Swal.fire({
                title: "Yakin hapus Cabang ini?",
                text: "Data akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: 'kt-btn kt-btn-danger me-2',
                    cancelButton: 'kt-btn kt-btn-outline',
                    popup: 'kt-modal-content',
                    header: 'kt-modal-header',
                    title: 'kt-modal-title'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    router.delete(`/branches/${id}`, {
                        preserveScroll: true,
                        onSuccess: () => {
                            window.Swal.fire({
                                title: "Terhapus!",
                                text: "Data cabang berhasil dihapus.",
                                icon: "success",
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        } else {
            if (confirm('Yakin hapus Cabang ini? Data akan dihapus permanen!')) {
                router.delete(`/branches/${id}`, { preserveScroll: true });
            }
        }
    };

    const from = branches.from || 0;
    const to = branches.to || 0;
    const total = branches.total || 0;

    return (
        <div className="grid gap-5 lg:gap-7.5">
            <Head title="Daftar Cabang (Branch)" />

            <div className="kt-card min-w-full">
                {/* Header & Toolbar */}
                <div className="kt-card-header flex-wrap justify-between gap-2">
                    <h3 className="kt-card-title text-base font-bold">List Cabang</h3>
                    <div className="flex flex-wrap items-center gap-2 lg:gap-5">
                        <form onSubmit={handleFilterSubmit} className="flex flex-wrap gap-2.5">
                            <div className="flex">
                                <label className="kt-input">
                                    <i className="ki-filled ki-magnifier"></i>
                                    <input
                                        type="text"
                                        placeholder="Cari Cabang..."
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        onBlur={handleFilterSubmit}
                                        onKeyDown={e => e.key === 'Enter' && handleFilterSubmit(e)}
                                    />
                                </label>
                            </div>
                            <select className="kt-select w-36" value={status} onChange={handleStatusChange}>
                                <option value="">Semua status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                            <select className="kt-select w-36" value={sort} onChange={handleSortChange}>
                                <option value="">Urutkan</option>
                                <option value="latest">Terbaru</option>
                                <option value="older">Terlama</option>
                                <option value="name_asc">Nama (A-Z)</option>
                            </select>
                        </form>

                        <Link href="/branches/create" className="kt-btn kt-btn-primary">
                            <i className="ki-filled ki-plus-square"></i> Tambah Cabang
                        </Link>
                    </div>
                </div>

                {/* Table */}
                <div className="kt-card-table kt-scrollable-x-auto pb-3">
                    <table className="kt-table align-middle text-sm text-muted-foreground">
                        <thead>
                            <tr>
                                <th className="w-10 text-center font-normal py-3 text-secondary-foreground">No</th>
                                <th className="min-w-[120px] font-normal py-3 text-secondary-foreground">Kode</th>
                                <th className="min-w-[200px] font-normal py-3 text-secondary-foreground">Nama Cabang</th>
                                <th className="min-w-[150px] font-normal py-3 text-secondary-foreground">Tipe</th>
                                <th className="min-w-[150px] font-normal py-3 text-secondary-foreground">Kota</th>
                                <th className="w-[120px] font-normal py-3 text-secondary-foreground text-center">Status</th>
                                <th className="w-24 text-center font-normal py-3 text-secondary-foreground">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {branches.data.length > 0 ? (
                                branches.data.map((item, index) => (
                                    <tr key={item.id} className="border-b border-border hover:bg-muted/30 transition-colors">
                                        <td className="p-3 text-center align-middle">{from + index}</td>
                                        <td className="p-3 align-middle font-medium text-foreground">{item.code || '-'}</td>
                                        <td className="p-3 align-middle text-foreground">{item.name}</td>
                                        <td className="p-3 align-middle">{item.type || '-'}</td>
                                        <td className="p-3 align-middle">{item.city || '-'}</td>
                                        <td className="p-3 text-center align-middle">
                                            {item.status === 'active' ? (
                                                <span className="kt-badge kt-badge-success kt-badge-outline rounded-[30px]">
                                                    <span className="kt-badge-dot size-1.5"></span>Aktif
                                                </span>
                                            ) : (
                                                <span className="kt-badge kt-badge-danger kt-badge-outline rounded-[30px]">
                                                    <span className="kt-badge-dot size-1.5"></span>Nonaktif
                                                </span>
                                            )}
                                        </td>
                                        <td className="p-3 text-center align-middle">
                                            <div className="flex items-center gap-2 justify-center">
                                                <Link href={`/branches/${item.id}/edit`} className="kt-btn kt-btn-sm kt-btn-outline" title="Ubah">
                                                    <i className="ki-filled ki-notepad-edit text-xs"></i>
                                                </Link>
                                                <button onClick={() => handleDelete(item.id)} className="kt-btn kt-btn-sm kt-btn-outline kt-btn-danger" title="Hapus">
                                                    <i className="ki-filled ki-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="7" className="text-center p-10 text-muted-foreground">
                                        Data Cabang tidak ditemukan.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {total > 0 && (
                    <div className="kt-card-footer flex flex-wrap items-center justify-between gap-4">
                        <span className="text-sm text-muted-foreground">
                            Menampilkan {from} sampai {to} dari {total} Cabang
                        </span>
                        <div className="flex items-center gap-1">
                            {branches.links.map((link, i) => {
                                let label = link.label;
                                if (label.includes('&laquo;')) label = '<i class="ki-filled ki-left text-xs"></i>';
                                if (label.includes('&raquo;')) label = '<i class="ki-filled ki-right text-xs"></i>';
                                return (
                                    <Link
                                        key={i}
                                        href={link.url || '#'}
                                        className={`kt-btn kt-btn-sm kt-btn-icon ${link.active ? 'kt-btn-primary' : 'kt-btn-outline'} ${!link.url && 'opacity-50 cursor-not-allowed'}`}
                                        dangerouslySetInnerHTML={{ __html: label }}
                                        preserveState
                                    />
                                );
                            })}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
