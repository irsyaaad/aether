import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ shipments, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || '');
    const [sort, setSort] = useState(filters.sort || 'latest');

    useEffect(() => {
        const timer = setTimeout(() => {
            if (search !== (filters.search || '')) {
                router.get('/shipments', { search, status, sort }, { preserveState: true, replace: true });
            }
        }, 400); // 400ms debounce

        return () => clearTimeout(timer);
    }, [search]);

    const handleFilterSubmit = (e) => {
        if (e && e.preventDefault) e.preventDefault();
        router.get('/shipments', { search, status, sort }, { preserveState: true });
    };

    const handleStatusChange = (e) => {
        const newStatus = e.target.value;
        setStatus(newStatus);
        router.get('/shipments', { search, status: newStatus, sort }, { preserveState: true });
    };

    const handleSortChange = (e) => {
        const newSort = e.target.value;
        setSort(newSort);
        router.get('/shipments', { search, status, sort: newSort }, { preserveState: true });
    };

    const handleDelete = (id) => {
        if (typeof window !== 'undefined' && window.Swal) {
            window.Swal.fire({
                title: "Yakin hapus STT ini?",
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
                    router.delete(`/shipments/${id}`, {
                        preserveScroll: true,
                        onSuccess: () => {
                            window.Swal.fire({
                                title: "Terhapus!",
                                text: "Data STT berhasil dihapus.",
                                icon: "success",
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        } else {
            if (confirm('Yakin hapus STT ini? Data akan dihapus permanen!')) {
                router.delete(`/shipments/${id}`, { preserveScroll: true });
            }
        }
    };

    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    };

    const getStatusBadge = (status) => {
        switch(status) {
            case 'draft': return <span className="kt-badge kt-badge-warning kt-badge-outline rounded-[30px]"><span className="kt-badge-dot size-1.5"></span>Draft</span>;
            case 'processing': return <span className="kt-badge kt-badge-primary kt-badge-outline rounded-[30px]"><span className="kt-badge-dot size-1.5"></span>Processing</span>;
            case 'in_transit': return <span className="kt-badge kt-badge-info kt-badge-outline rounded-[30px]"><span className="kt-badge-dot size-1.5"></span>In Transit</span>;
            case 'delivered': return <span className="kt-badge kt-badge-success kt-badge-outline rounded-[30px]"><span className="kt-badge-dot size-1.5"></span>Delivered</span>;
            case 'cancelled': return <span className="kt-badge kt-badge-danger kt-badge-outline rounded-[30px]"><span className="kt-badge-dot size-1.5"></span>Cancelled</span>;
            default: return <span className="kt-badge kt-badge-outline rounded-[30px]">{status}</span>;
        }
    };

    const getPaymentBadge = (status) => {
        switch(status) {
            case 'unpaid': return <span className="kt-badge kt-badge-danger kt-badge-outline rounded-[30px]">Unpaid</span>;
            case 'partial': return <span className="kt-badge kt-badge-warning kt-badge-outline rounded-[30px]">Partial</span>;
            case 'paid': return <span className="kt-badge kt-badge-success kt-badge-outline rounded-[30px]">Paid</span>;
            default: return <span className="kt-badge kt-badge-outline rounded-[30px]">{status}</span>;
        }
    };

    const from = shipments.from || 0;
    const to = shipments.to || 0;
    const total = shipments.total || 0;

    return (
        <div className="grid gap-5 lg:gap-7.5">
            <Head title="Daftar Shipment / STT" />

            <div className="kt-card min-w-full">
                {/* Header & Toolbar */}
                <div className="kt-card-header flex-wrap justify-between gap-2">
                    <h3 className="kt-card-title text-base font-bold">List Shipment</h3>
                    <div className="flex flex-wrap items-center gap-2 lg:gap-5">
                        <form onSubmit={handleFilterSubmit} className="flex flex-wrap gap-2.5">
                            <div className="flex">
                                <label className="kt-input">
                                    <i className="ki-filled ki-magnifier"></i>
                                    <input
                                        type="text"
                                        placeholder="Cari STT/Customer..."
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        onBlur={handleFilterSubmit}
                                        onKeyDown={e => e.key === 'Enter' && handleFilterSubmit(e)}
                                    />
                                </label>
                            </div>
                            <select className="kt-select w-36" value={status} onChange={handleStatusChange}>
                                <option value="">Semua Status</option>
                                <option value="draft">Draft</option>
                                <option value="processing">Processing</option>
                                <option value="in_transit">In Transit</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <select className="kt-select w-36" value={sort} onChange={handleSortChange}>
                                <option value="latest">Terbaru</option>
                                <option value="older">Terlama</option>
                            </select>
                            <button type="submit" className="kt-btn kt-btn-outline kt-btn-primary">
                                <i className="ki-filled ki-setting-4"></i> Filters
                            </button>
                        </form>
                        <Link href="/shipments/create" className="kt-btn kt-btn-sm kt-btn-primary">
                            <i className="ki-filled ki-plus"></i> Tambah Shipment
                        </Link>
                    </div>
                </div>

                {/* Table Content */}
                <div className="kt-card-body p-0">
                    <div className="kt-scrollable-x-auto min-h-[300px]">
                        <table className="kt-table table-auto kt-table-border w-full" style={{ width: '100%' }}>
                            <thead>
                                <tr>
                                    <th className="w-[60px] text-center p-3">
                                        <input className="kt-checkbox kt-checkbox-sm" type="checkbox" />
                                    </th>
                                    <th className="min-w-[200px] p-3 text-secondary-foreground font-semibold text-left">
                                        No. STT<br/><span className="text-xs font-normal">Customer / Tgl</span>
                                    </th>
                                    <th className="min-w-[150px] p-3 text-secondary-foreground font-semibold text-left">Rute</th>
                                    <th className="min-w-[170px] p-3 text-secondary-foreground font-semibold text-left">
                                        Service<br/><span className="text-xs font-normal">Biaya Kirim</span>
                                    </th>
                                    <th className="min-w-[160px] p-3 text-secondary-foreground font-semibold text-left">
                                        Status Bayar<br/><span className="text-xs font-normal">Status Kirim</span>
                                    </th>
                                    <th className="w-[80px] text-center p-3 text-secondary-foreground font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {shipments.data.map(shipment => (
                                    <tr key={shipment.id} className="hover:bg-muted/50 transition-colors border-b border-border last:border-0">
                                        <td className="text-center p-3 align-middle">
                                            <input className="kt-checkbox kt-checkbox-sm" type="checkbox" value={shipment.id} />
                                        </td>
                                        <td className="p-3 align-middle text-sm text-foreground">
                                            <a href={`/shipments/${shipment.id}/detail`} className="text-primary font-semibold hover:underline block mb-1">
                                                {shipment.stt_number}
                                            </a>
                                            <div className="text-xs text-muted-foreground">
                                                {shipment.customer?.name || 'Umum'} / {new Date(shipment.shipment_date || shipment.created_at).toLocaleDateString('id-ID')}
                                            </div>
                                        </td>
                                        <td className="p-3 align-middle">
                                            <div className="flex items-center gap-1 flex-wrap">
                                                <span className="badge bg-light text-dark text-xs py-1 px-2 rounded">
                                                    {(typeof shipment.sender_city === 'object' ? shipment.sender_city?.name : shipment.sender_city) || '-'}
                                                </span>
                                                <i className="ki-filled ki-arrow-right text-xs text-muted-foreground"></i>
                                                <span className="badge bg-light text-dark text-xs py-1 px-2 rounded">
                                                    {(typeof shipment.receiver_city === 'object' ? shipment.receiver_city?.name : shipment.receiver_city) || '-'}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="p-3 align-middle text-sm text-foreground">
                                            <div className="font-medium mb-1">{(typeof shipment.service === 'object' ? shipment.service?.nama_layanan : shipment.service_type) || shipment.service_type || '-'}</div>
                                            <div className="text-xs font-semibold text-success">{formatRupiah(shipment.shipping_cost || 0)}</div>
                                        </td>
                                        <td className="p-3 align-middle">
                                            <div className="flex flex-col gap-1.5 items-start">
                                                {getPaymentBadge(shipment.payment_status)}
                                                {getStatusBadge(shipment.status)}
                                            </div>
                                        </td>
                                        <td className="p-3 align-middle text-center">
                                            <div className="flex items-center justify-center gap-2">
                                                <a href={`/shipments/${shipment.id}/print`} target="_blank" className="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost hover:text-success" title="Cetak STT">
                                                    <i className="ki-filled ki-printer text-lg"></i>
                                                </a>
                                                <Link href={`/shipments/${shipment.id}/edit`} className="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost hover:text-primary" title="Edit">
                                                    <i className="ki-filled ki-pencil text-lg"></i>
                                                </Link>
                                                <button onClick={() => handleDelete(shipment.id)} className="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost hover:text-danger" title="Hapus">
                                                    <i className="ki-filled ki-trash text-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}

                                {shipments.data.length === 0 && (
                                    <tr>
                                        <td colSpan="6" className="p-12 text-center text-muted-foreground font-medium">
                                            Menampilkan 0 Shipment
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Pagination Footer */}
                <div className="flex justify-between items-center px-6 py-4 border-t border-border flex-wrap gap-4">
                    <div className="text-sm text-secondary-foreground">
                        {total > 0 ? (
                            `Menampilkan ${from} sampai ${to} dari ${total} Shipment`
                        ) : (
                            'Menampilkan 0 Shipment'
                        )}
                    </div>
                    
                    {shipments.links && shipments.links.length > 3 && (
                        <div className="flex gap-1 items-center">
                            {shipments.links.map((link, i) => {
                                let label = link.label;
                                if (label.includes('&laquo;')) label = '<i class="ki-filled ki-left text-xs"></i>';
                                if (label.includes('&raquo;')) label = '<i class="ki-filled ki-right text-xs"></i>';
                                return (
                                    <Link
                                        key={i}
                                        href={link.url || '#'}
                                        className={`kt-btn kt-btn-sm kt-btn-icon ${link.active ? 'kt-btn-primary' : 'kt-btn-outline'} ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                                        dangerouslySetInnerHTML={{ __html: label }}
                                        preserveState
                                    />
                                );
                            })}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
