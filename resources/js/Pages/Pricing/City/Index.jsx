import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ cityPricings, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || '');
    const [sort, setSort] = useState(filters.sort || 'latest');

    useEffect(() => {
        const timer = setTimeout(() => {
            if (search !== (filters.search || '')) {
                router.get('/city-pricing', { search, status, sort }, { preserveState: true, replace: true });
            }
        }, 400);

        return () => clearTimeout(timer);
    }, [search]);

    const handleFilterSubmit = (e) => {
        if (e && e.preventDefault) e.preventDefault();
        router.get('/city-pricing', { search, status, sort }, { preserveState: true });
    };

    const handleStatusChange = (e) => {
        const newStatus = e.target.value;
        setStatus(newStatus);
        router.get('/city-pricing', { search, status: newStatus, sort }, { preserveState: true });
    };

    const handleSortChange = (e) => {
        const newSort = e.target.value;
        setSort(newSort);
        router.get('/city-pricing', { search, status, sort: newSort }, { preserveState: true });
    };

    const handleDelete = (id) => {
        if (typeof window !== 'undefined' && window.Swal) {
            window.Swal.fire({
                title: "Yakin hapus harga ini?",
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
                    router.delete(`/city-pricing/${id}`, {
                        preserveScroll: true,
                        onSuccess: (page) => {
                            if (page.props.flash && page.props.flash.error) {
                                window.Swal.fire({
                                    title: "Gagal!",
                                    text: page.props.flash.error,
                                    icon: "error",
                                    confirmButtonText: "Tutup"
                                });
                            } else {
                                window.Swal.fire({
                                    title: "Terhapus!",
                                    text: "Data pricing berhasil dihapus.",
                                    icon: "success",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        }
                    });
                }
            });
        } else {
            if (confirm('Yakin hapus Harga ini? Data akan dihapus permanen!')) {
                router.delete(`/city-pricing/${id}`, { preserveScroll: true });
            }
        }
    };

    const formatRupiah = (number) => {
        if (!number) return '-';
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
    };

    const from = cityPricings.from || 0;
    const to = cityPricings.to || 0;
    const total = cityPricings.total || 0;

    return (
        <div className="grid gap-5 lg:gap-7.5">
            <Head title="City Pricing (Harga Kota)" />

            <div className="kt-card min-w-full">
                {/* Header & Toolbar */}
                <div className="kt-card-header flex-wrap justify-between gap-2">
                    <h3 className="kt-card-title text-base font-bold">List City Pricing</h3>
                    <div className="flex flex-wrap items-center gap-2 lg:gap-5">
                        <form onSubmit={handleFilterSubmit} className="flex flex-wrap gap-2.5">
                            <div className="flex">
                                <label className="kt-input">
                                    <i className="ki-filled ki-magnifier"></i>
                                    <input
                                        type="text"
                                        placeholder="Cari Kota / Layanan..."
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
                                <option value="latest">Terbaru</option>
                                <option value="older">Terlama</option>
                            </select>
                        </form>

                        <Link href="/city-pricing/create" className="kt-btn kt-btn-primary">
                            <i className="ki-filled ki-plus-square"></i> Tambah Harga
                        </Link>
                    </div>
                </div>

                {/* Table */}
                <div className="kt-card-table kt-scrollable-x-auto pb-3">
                    <table className="kt-table align-middle text-sm text-muted-foreground">
                        <thead>
                            <tr>
                                <th className="w-10 text-center font-normal py-3 text-secondary-foreground">No</th>
                                <th className="min-w-[150px] font-normal py-3 text-secondary-foreground">Layanan</th>
                                <th className="min-w-[250px] font-normal py-3 text-secondary-foreground">Rute (Asal - Tujuan)</th>
                                <th className="min-w-[120px] font-normal py-3 text-secondary-foreground">Harga / Kg</th>
                                <th className="min-w-[120px] font-normal py-3 text-secondary-foreground">Harga / CBM</th>
                                <th className="min-w-[120px] font-normal py-3 text-secondary-foreground">Min Charge</th>
                                <th className="w-[100px] font-normal py-3 text-secondary-foreground text-center">Status</th>
                                <th className="w-24 text-center font-normal py-3 text-secondary-foreground">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {cityPricings.data.length > 0 ? (
                                cityPricings.data.map((item, index) => (
                                    <tr key={item.id} className="border-b border-border hover:bg-muted/30 transition-colors">
                                        <td className="p-3 text-center align-middle">{from + index}</td>
                                        <td className="p-3 align-middle font-medium text-foreground">
                                            {item.service?.nama_layanan || item.service_type || '-'}
                                        </td>
                                        <td className="p-3 align-middle text-foreground">
                                            <span className="bg-muted text-foreground text-xs px-2 py-1 rounded border border-border inline-block">
                                                {item.origin_regency?.name || item.origin_city}
                                            </span>
                                            <i className="ki-filled ki-arrow-right text-xs mx-1 text-muted-foreground"></i>
                                            <span className="bg-muted text-foreground text-xs px-2 py-1 rounded border border-border inline-block">
                                                {item.destination_regency?.name || item.destination_city}
                                            </span>
                                        </td>
                                        <td className="p-3 align-middle">{formatRupiah(item.price_per_kg)}</td>
                                        <td className="p-3 align-middle">{formatRupiah(item.price_per_cubic)}</td>
                                        <td className="p-3 align-middle">{formatRupiah(item.min_charge)}</td>
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
                                                <Link href={`/city-pricing/${item.id}/edit`} className="kt-btn kt-btn-sm kt-btn-outline" title="Ubah">
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
                                    <td colSpan="8" className="text-center p-10 text-muted-foreground">
                                        Data City Pricing tidak ditemukan.
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
                            Menampilkan {from} sampai {to} dari {total} Harga
                        </span>
                        <div className="flex items-center gap-1">
                            {cityPricings.links.map((link, i) => {
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
