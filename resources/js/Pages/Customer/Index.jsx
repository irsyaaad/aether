import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ customers, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || '');
    const [sort, setSort] = useState(filters.sort || 'name_asc');

    useEffect(() => {
        const timer = setTimeout(() => {
            if (search !== (filters.search || '')) {
                router.get('/customers', { search, status, sort }, { preserveState: true, replace: true });
            }
        }, 400); // 400ms debounce

        return () => clearTimeout(timer);
    }, [search]);

    const handleFilterSubmit = (e) => {
        if (e && e.preventDefault) e.preventDefault();
        router.get('/customers', { search, status, sort }, { preserveState: true });
    };

    const handleStatusChange = (e) => {
        const newStatus = e.target.value;
        setStatus(newStatus);
        router.get('/customers', { search, status: newStatus, sort }, { preserveState: true });
    };

    const handleSortChange = (e) => {
        const newSort = e.target.value;
        setSort(newSort);
        router.get('/customers', { search, status, sort: newSort }, { preserveState: true });
    };

    const handleDelete = (id) => {
        if (typeof window !== 'undefined' && window.Swal) {
            window.Swal.fire({
                title: "Yakin hapus Customer ini?",
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
                    router.delete(`/customers/${id}`, {
                        preserveScroll: true,
                        onSuccess: () => {
                            window.Swal.fire({
                                title: "Terhapus!",
                                text: "Data customer berhasil dihapus.",
                                icon: "success",
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        } else {
            // Fallback just in case Swal isn't loaded
            if (confirm('Yakin hapus Customer ini? Data akan dihapus permanen!')) {
                router.delete(`/customers/${id}`, { preserveScroll: true });
            }
        }
    };

    // Calculate pagination info (e.g. "Menampilkan 1 sampai 12 dari 50 Customer")
    const from = customers.from || 0;
    const to = customers.to || 0;
    const total = customers.total || 0;

    return (
        <div className="grid gap-5 lg:gap-7.5">
            <Head title="Daftar Customer" />

            <div className="kt-card min-w-full">
                {/* Header & Toolbar */}
                <div className="kt-card-header flex-wrap justify-between gap-2">
                    <h3 className="kt-card-title text-base font-bold">List Customer</h3>
                    <div className="flex flex-wrap items-center gap-2 lg:gap-5">
                        <form onSubmit={handleFilterSubmit} className="flex flex-wrap gap-2.5">
                            <div className="flex">
                                <label className="kt-input">
                                    <i className="ki-filled ki-magnifier"></i>
                                    <input
                                        type="text"
                                        placeholder="Cari Customer..."
                                        value={search}
                                        onChange={e => {
                                            setSearch(e.target.value);
                                        }}
                                        onBlur={handleFilterSubmit}
                                        onKeyDown={e => e.key === 'Enter' && handleFilterSubmit(e)}
                                    />
                                </label>
                            </div>
                            <select className="kt-select w-36" value={status} onChange={handleStatusChange}>
                                <option value="">Pilih status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                            <select className="kt-select w-36" value={sort} onChange={handleSortChange}>
                                <option value="">Urutkan</option>
                                <option value="latest">Terbaru</option>
                                <option value="older">Terlama</option>
                                <option value="name_asc">Nama (A-Z)</option>
                            </select>
                            <button type="submit" className="kt-btn kt-btn-outline kt-btn-primary">
                                <i className="ki-filled ki-setting-4"></i> Filters
                            </button>
                        </form>
                        <Link href="/customers/create" className="kt-btn kt-btn-sm kt-btn-primary">
                            <i className="ki-filled ki-plus"></i> Tambah Customer
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
                                    <th className="min-w-[120px] p-3 text-secondary-foreground font-semibold text-left">Kode</th>
                                    <th className="min-w-[180px] p-3 text-secondary-foreground font-semibold text-left">Nama</th>
                                    <th className="min-w-[140px] p-3 text-secondary-foreground font-semibold text-left">Telepon</th>
                                    <th className="min-w-[120px] p-3 text-secondary-foreground font-semibold text-left">Kota</th>
                                    <th className="min-w-[100px] p-3 text-secondary-foreground font-semibold text-left">Tipe</th>
                                    <th className="min-w-[90px] p-3 text-secondary-foreground font-semibold text-left">Status</th>
                                    <th className="w-[80px] text-center p-3 text-secondary-foreground font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {customers.data.map(customer => (
                                    <tr key={customer.id} className="hover:bg-muted/50 transition-colors border-b border-border last:border-0">
                                        <td className="text-center p-3 align-middle">
                                            <input className="kt-checkbox kt-checkbox-sm" type="checkbox" value={customer.id} />
                                        </td>
                                        <td className="p-3 align-middle text-sm text-foreground">{customer.code}</td>
                                        <td className="p-3 align-middle">
                                            <Link href={`/customers/${customer.id}/detail`} className="text-primary font-semibold hover:underline text-sm">
                                                {customer.name}
                                            </Link>
                                        </td>
                                        <td className="p-3 align-middle text-sm text-foreground">{customer.phone || '-'}</td>
                                        <td className="p-3 align-middle text-sm text-foreground">{customer.regency?.name || customer.city || '-'}</td>
                                        <td className="p-3 align-middle text-sm text-foreground uppercase">{customer.type}</td>
                                        <td className="p-3 align-middle">
                                            {customer.status === 'active' ? (
                                                <span className="kt-badge kt-badge-success kt-badge-outline rounded-[30px]">
                                                    <span className="kt-badge-dot size-1.5"></span>Aktif
                                                </span>
                                            ) : (
                                                <span className="kt-badge kt-badge-destructive kt-badge-outline rounded-[30px]">
                                                    <span className="kt-badge-dot size-1.5"></span>Nonaktif
                                                </span>
                                            )}
                                        </td>
                                        <td className="p-3 align-middle text-center">
                                            <div className="flex items-center justify-center gap-2">
                                                <Link href={`/customers/${customer.id}/edit`} className="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost hover:text-primary">
                                                    <i className="ki-filled ki-pencil text-lg"></i>
                                                </Link>
                                                <button onClick={() => handleDelete(customer.id)} className="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost hover:text-danger">
                                                    <i className="ki-filled ki-trash text-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}

                                {customers.data.length === 0 && (
                                    <tr>
                                        <td colSpan="8" className="p-12 text-center text-muted-foreground font-medium">
                                            Menampilkan 0 Customer
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Pagination Footer matches DataTables layout */}
                <div className="flex justify-between items-center px-6 py-4 border-t border-border flex-wrap gap-4">
                    <div className="text-sm text-secondary-foreground">
                        {total > 0 ? (
                            `Menampilkan ${from} sampai ${to} dari ${total} Customer`
                        ) : (
                            'Menampilkan 0 Customer'
                        )}
                    </div>
                    
                    {customers.links && customers.links.length > 3 && (
                        <div className="flex gap-1 items-center">
                            {customers.links.map((link, i) => {
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
