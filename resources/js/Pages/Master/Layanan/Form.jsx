import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ layanan }) {
    const isEdit = !!layanan;
    
    const { data, setData, post, put, processing, errors } = useForm({
        kode_layanan: layanan?.kode_layanan || '',
        nama_layanan: layanan?.nama_layanan || '',
        status: layanan?.status || 'active'
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        
        const options = {
            onSuccess: () => {
                if (typeof window !== 'undefined' && window.Swal) {
                    window.Swal.fire({
                        title: "Berhasil!",
                        text: isEdit ? "Data layanan berhasil diubah." : "Layanan baru berhasil ditambahkan.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }
        };

        if (isEdit) {
            put(`/master/layanan/${layanan.kode_layanan}`, options);
        } else {
            post('/master/layanan', options);
        }
    };

    return (
        <div className="w-full pb-10 max-w-3xl mx-auto">
            <Head title={isEdit ? 'Edit Layanan' : 'Tambah Layanan'} />
            
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h2 className="text-xl font-bold text-foreground">{isEdit ? 'Ubah Data Layanan' : 'Tambah Layanan Baru'}</h2>
                </div>
                <Link href="/master/layanan" className="kt-btn kt-btn-outline">
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
                <div className="kt-card min-w-full">
                    <div className="kt-card-header">
                        <h3 className="kt-card-title text-base font-bold">Informasi Layanan</h3>
                    </div>
                    <div className="kt-card-table kt-scrollable-x-auto pb-3">
                        <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                            <tbody>
                                <tr>
                                    <td className="w-32 sm:w-48 text-secondary-foreground font-normal py-4 px-2 sm:px-4 border-b border-border">Kode Layanan *</td>
                                    <td className="text-foreground font-normal py-4 px-2 sm:px-4 border-b border-border">
                                        <input 
                                            type="text" 
                                            className={`kt-input w-full ${isEdit ? 'bg-muted/20' : ''}`} 
                                            value={data.kode_layanan} 
                                            onChange={e => setData('kode_layanan', e.target.value)} 
                                            readOnly={isEdit}
                                            required 
                                        />
                                        {isEdit && <span className="text-xs text-muted-foreground mt-1 block">Kode layanan tidak dapat diubah setelah dibuat.</span>}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="w-32 sm:w-48 text-secondary-foreground font-normal py-4 px-2 sm:px-4 border-b border-border">Nama Layanan *</td>
                                    <td className="text-foreground font-normal py-4 px-2 sm:px-4 border-b border-border">
                                        <input 
                                            type="text" 
                                            className="kt-input w-full" 
                                            value={data.nama_layanan} 
                                            onChange={e => setData('nama_layanan', e.target.value)} 
                                            required 
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td className="w-32 sm:w-48 text-secondary-foreground font-normal py-4 px-2 sm:px-4">Status *</td>
                                    <td className="text-foreground font-normal py-4 px-2 sm:px-4">
                                        <select className="kt-select w-full" value={data.status} onChange={e => setData('status', e.target.value)} required>
                                            <option value="active">Aktif</option>
                                            <option value="inactive">Nonaktif</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div className="kt-card-footer flex items-center justify-end gap-3 py-4">
                        <Link href="/master/layanan" className="kt-btn kt-btn-outline grow md:grow-0 justify-center">
                            Batal
                        </Link>
                        <button type="submit" disabled={processing} className="kt-btn kt-btn-primary justify-center">
                            <i className="ki-filled ki-check text-sm"></i> 
                            {processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Simpan')}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    );
}
