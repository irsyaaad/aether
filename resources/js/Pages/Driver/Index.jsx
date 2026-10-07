import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ drivers, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || '');
    const [sort, setSort] = useState(filters.sort || 'name_asc');

    useEffect(() => {
        const timer = setTimeout(() => {
            if (search !== (filters.search || '')) {
                router.get('/drivers', { search, status, sort }, { preserveState: true, replace: true });
            }
        }, 400);

        return () => clearTimeout(timer);
    }, [search]);

    const handleFilterSubmit = (e) => {
        if (e && e.preventDefault) e.preventDefault();
        router.get('/drivers', { search, status, sort }, { preserveState: true });
    };

    const handleStatusChange = (e) => {
        const newStatus = e.target.value;
        setStatus(newStatus);
        router.get('/drivers', { search, status: newStatus, sort }, { preserveState: true });
    };

    const handleSortChange = (e) => {
        const newSort = e.target.value;
        setSort(newSort);
        router.get('/drivers', { search, status, sort: newSort }, { preserveState: true });
    };

    const handleDelete = (id) => {
        if (typeof window !== 'undefined' && window.Swal) {
            window.Swal.fire({
                title: "Yakin hapus Driver ini?",
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
                    router.delete(`/drivers/${id}`, {
                        preserveScroll: true,
                        onSuccess: () => {
                            window.Swal.fire({
                                title: "Terhapus!",
                                text: "Data driver berhasil dihapus.",
                                icon: "success",
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        } else {
            if (confirm('Yakin hapus Driver ini? Data akan dihapus permanen!')) {
                router.delete(`/drivers/${id}`, { preserveScroll: true });
            }
        }
    };

    const from = drivers.from || 0;
    const to = drivers.to || 0;
    const total = drivers.total || 0;

    return (
        <div className="grid gap-5 lg:gap-7.5">
            <Head title="Daftar Sopir (Driver)" />

            <div className="kt-card min-w-full">
                {/* Header & Toolbar */}
                <div className="kt-card-header flex-wrap justify-between gap-2">
                    <h3 className="kt-card-title text-base font-bold">List Sopir (Driver)</h3>
                    <div className="flex flex-wrap items-center gap-2 lg:gap-5">
                        <form onSubmit={handleFilterSubmit} className="flex flex-wrap gap-2.5">
                            <div className="flex">
                                <label className="kt-input">
                                    <i className="ki-filled ki-magnifier"></i>
                                    <input
                                        type="text"
                                        placeholder="Cari Sopir..."
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        onBlur={handleFilterSubmit}
                                        onKeyDown={e => e.key === 'Enter' && handleFilterSubmit(e)}
                                    />
                                </label>
                            </div>
                            <select className="kt-select w-36" value={status} onChange={handleStatusChange}>
                                <option value="">Semua status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            <select className="kt-select w-36" value={sort} onChange={handleSortChange}>
                                <option value="">Urutkan</option>
                                <option value="latest">Terbaru</option>
                                <option value="older">Terlama</option>
                                <option value="name_asc">Nama (A-Z)</option>
                            </select>
                        </form>

                        <Link href="/drivers/create" className="kt-btn kt-btn-primary">
                            <i className="ki-filled ki-plus-square"></i> Tambah Sopir
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
                                <th className="min-w-[200px] font-normal py-3 text-secondary-foreground">Nama Sopir</th>
                                <th className="min-w-[150px] font-normal py-3 text-secondary-foreground">Telepon</th>
                                <th className="min-w-[150px] font-normal py-3 text-secondary-foreground">No. SIM</th>
                                <th className="w-[120px] font-normal py-3 text-secondary-foreground text-center">Status</th>
                                <th className="w-24 text-center font-normal py-3 text-secondary-foreground">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {drivers.data.length > 0 ? (
                                drivers.data.map((item, index) => (
                                    <tr key={item.id} className="border-b border-border hover:bg-muted/30 transition-colors">
                                        <td className="p-3 text-center align-middle">{from + index}</td>
                                        <td className="p-3 align-middle font-medium text-foreground">{item.code || '-'}</td>
                                        <td className="p-3 align-middle text-foreground">{item.name}</td>
                                        <td className="p-3 align-middle">{item.phone || '-'}</td>
                                        <td className="p-3 align-middle">{item.license_number || '-'} ({item.license_type || '-'})</td>
                                        <td className="p-3 text-center align-middle">
                                            {item.status === 'aktif' ? (
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
                                                <Link href={`/drivers/${item.id}/edit`} className="kt-btn kt-btn-sm kt-btn-outline" title="Ubah">
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
                                        Data Sopir tidak ditemukan.
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
                            Menampilkan {from} sampai {to} dari {total} Sopir
                        </span>
                        <div className="flex items-center gap-1">
                            {drivers.links.map((link, i) => {
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
