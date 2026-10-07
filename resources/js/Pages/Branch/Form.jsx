import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ branch }) {
    const isEdit = !!branch;
    
    const { data, setData, post, put, processing, errors } = useForm({
        code: branch?.code || '',
        name: branch?.name || '',
        phone: branch?.phone || '',
        email: branch?.email || '',
        address: branch?.address || '',
        province: branch?.province || '',
        city: branch?.city || '',
        type: branch?.type || '',
        status: branch?.status || 'active',
        notes: branch?.notes || ''
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        
        const options = {
            onSuccess: () => {
                if (typeof window !== 'undefined' && window.Swal) {
                    window.Swal.fire({
                        title: "Berhasil!",
                        text: isEdit ? "Data cabang berhasil diubah." : "Cabang baru berhasil ditambahkan.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }
        };

        if (isEdit) {
            put(`/branches/${branch.id}`, options);
        } else {
            post('/branches', options);
        }
    };

    return (
        <div className="w-full pb-10">
            <Head title={isEdit ? 'Edit Cabang' : 'Tambah Cabang'} />
            
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h2 className="text-xl font-bold text-foreground">{isEdit ? 'Ubah Data Cabang' : 'Tambah Cabang Baru'}</h2>
                </div>
                <Link href="/branches" className="kt-btn kt-btn-outline">
                    <i className="ki-filled ki-arrow-left"></i> Kembali
                </Link>
            </div>

            {Object.keys(errors).length > 0 && (
                <div className="kt-alert kt-alert-destructive mb-6">
                    <div className="kt-alert-content">
                        <div className="kt-alert-description">
                            <ul className="list-disc ps-4 text-sm">
                                {Object.values(errors).map((error, index) => (
                                    <li key={index}>{error}</li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            )}

            <form onSubmit={handleSubmit}>
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-7.5">
                    
                    {/* Kolom Kiri */}
                    <div className="flex flex-col gap-5 lg:gap-7.5">
                        <div className="kt-card min-w-full">
                            <div className="kt-card-header">
                                <h3 className="kt-card-title text-base font-bold">Informasi Utama</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Kode</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.code} onChange={e => setData('code', e.target.value)} placeholder="(Opsional)" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Nama Cabang *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.name} onChange={e => setData('name', e.target.value)} required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Telepon</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.phone} onChange={e => setData('phone', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Email</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="email" className="kt-input w-full" value={data.email} onChange={e => setData('email', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Tipe</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.type} onChange={e => setData('type', e.target.value)} placeholder="Contoh: Pusat, Agen, Gudang" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4">Status *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4">
                                                <select className="kt-select w-full" value={data.status} onChange={e => setData('status', e.target.value)} required>
                                                    <option value="active">Aktif</option>
                                                    <option value="inactive">Nonaktif</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {/* Kolom Kanan */}
                    <div className="flex flex-col gap-5 lg:gap-7.5">
                        <div className="kt-card min-w-full">
                            <div className="kt-card-header">
                                <h3 className="kt-card-title text-base font-bold">Detail Alamat</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Alamat</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <textarea className="kt-input w-full min-h-[70px] resize-y py-2" value={data.address} onChange={e => setData('address', e.target.value)}></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Kota</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.city} onChange={e => setData('city', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Provinsi</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.province} onChange={e => setData('province', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4">Notes</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4">
                                                <textarea className="kt-input w-full min-h-[50px] resize-y py-2" value={data.notes} onChange={e => setData('notes', e.target.value)}></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div className="flex items-center justify-end gap-3 pt-2 mt-4">
                            <Link href="/branches" className="kt-btn kt-btn-outline grow md:grow-0 justify-center">
                                Batal
                            </Link>
                            <button type="submit" disabled={processing} className="kt-btn kt-btn-primary grow md:grow-0 justify-center">
                                <i className="ki-filled ki-check text-sm"></i> 
                                {processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Simpan')}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    );
}
