import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ driver }) {
    const isEdit = !!driver;
    
    // Convert dates from DB if exists
    const formatDate = (dateString) => {
        if (!dateString) return '';
        return dateString.substring(0, 10);
    };

    const { data, setData, post, put, processing, errors } = useForm({
        code: driver?.code || '',
        name: driver?.name || '',
        phone: driver?.phone || '',
        email: driver?.email || '',
        address: driver?.address || '',
        license_number: driver?.license_number || '',
        license_type: driver?.license_type || '',
        license_expired: formatDate(driver?.license_expired),
        id_card_number: driver?.id_card_number || '',
        birth_date: formatDate(driver?.birth_date),
        base_salary: driver?.base_salary || '',
        status: driver?.status || 'aktif',
        notes: driver?.notes || ''
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        
        const options = {
            onSuccess: () => {
                if (typeof window !== 'undefined' && window.Swal) {
                    window.Swal.fire({
                        title: "Berhasil!",
                        text: isEdit ? "Data sopir berhasil diubah." : "Sopir baru berhasil ditambahkan.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }
        };

        if (isEdit) {
            put(`/drivers/${driver.id}`, options);
        } else {
            post('/drivers', options);
        }
    };

    return (
        <div className="w-full pb-10">
            <Head title={isEdit ? 'Edit Sopir' : 'Tambah Sopir'} />
            
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h2 className="text-xl font-bold text-foreground">{isEdit ? 'Ubah Data Sopir' : 'Tambah Sopir Baru'}</h2>
                </div>
                <Link href="/drivers" className="kt-btn kt-btn-outline">
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
                                                <input type="text" className="kt-input bg-muted/20 w-full" value={data.code} readOnly placeholder="(Auto)" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Nama Sopir *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.name} onChange={e => setData('name', e.target.value)} required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">No. KTP</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.id_card_number} onChange={e => setData('id_card_number', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Tgl Lahir</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="date" className="kt-input w-full" value={data.birth_date} onChange={e => setData('birth_date', e.target.value)} />
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
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Gaji Pokok</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="number" className="kt-input w-full" value={data.base_salary} onChange={e => setData('base_salary', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4">Status *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4">
                                                <select className="kt-select w-full" value={data.status} onChange={e => setData('status', e.target.value)} required>
                                                    <option value="aktif">Aktif</option>
                                                    <option value="nonaktif">Nonaktif</option>
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
                                <h3 className="kt-card-title text-base font-bold">Lisensi & Alamat</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">No. SIM</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.license_number} onChange={e => setData('license_number', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Tipe SIM</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.license_type} onChange={e => setData('license_type', e.target.value)} placeholder="Contoh: A, B1 Umum, dll" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Masa Berlaku SIM</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="date" className="kt-input w-full" value={data.license_expired} onChange={e => setData('license_expired', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Alamat Lengkap</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <textarea className="kt-input w-full min-h-[70px] resize-y py-2" value={data.address} onChange={e => setData('address', e.target.value)}></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4">Catatan Tambahan</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4">
                                                <textarea className="kt-input w-full min-h-[50px] resize-y py-2" value={data.notes} onChange={e => setData('notes', e.target.value)}></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div className="flex items-center justify-end gap-3 pt-2 mt-4">
                            <Link href="/drivers" className="kt-btn kt-btn-outline grow md:grow-0 justify-center">
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
