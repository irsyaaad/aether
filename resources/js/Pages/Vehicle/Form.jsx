import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ vehicle }) {
    const isEdit = !!vehicle;
    
    // Convert dates from DB if exists
    const formatDate = (dateString) => {
        if (!dateString) return '';
        return dateString.substring(0, 10);
    };

    const { data, setData, post, put, processing, errors } = useForm({
        code: vehicle?.code || '',
        plate_number: vehicle?.plate_number || '',
        brand: vehicle?.brand || '',
        model: vehicle?.model || '',
        year: vehicle?.year || '',
        vehicle_type: vehicle?.vehicle_type || '',
        color: vehicle?.color || '',
        stnk_number: vehicle?.stnk_number || '',
        stnk_expiry: formatDate(vehicle?.stnk_expiry),
        kir_number: vehicle?.kir_number || '',
        kir_expiry: formatDate(vehicle?.kir_expiry),
        ownership: vehicle?.ownership || '',
        max_capacity: vehicle?.max_capacity || '',
        max_volume: vehicle?.max_volume || '',
        status: vehicle?.status || 'active',
        notes: vehicle?.notes || ''
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        
        const options = {
            onSuccess: () => {
                if (typeof window !== 'undefined' && window.Swal) {
                    window.Swal.fire({
                        title: "Berhasil!",
                        text: isEdit ? "Data kendaraan berhasil diubah." : "Kendaraan baru berhasil ditambahkan.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }
        };

        if (isEdit) {
            put(`/vehicles/${vehicle.id}`, options);
        } else {
            post('/vehicles', options);
        }
    };

    return (
        <div className="w-full pb-10">
            <Head title={isEdit ? 'Edit Armada' : 'Tambah Armada'} />
            
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h2 className="text-xl font-bold text-foreground">{isEdit ? 'Ubah Data Armada' : 'Tambah Armada Baru'}</h2>
                </div>
                <Link href="/vehicles" className="kt-btn kt-btn-outline">
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
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">No. Polisi *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full uppercase" value={data.plate_number} onChange={e => setData('plate_number', e.target.value.toUpperCase())} placeholder="B 1234 CD" required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Merek</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.brand} onChange={e => setData('brand', e.target.value)} placeholder="Contoh: Hino, Mitsubishi" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Model / Varian</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.model} onChange={e => setData('model', e.target.value)} placeholder="Contoh: Canter 125" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Tipe Kendaraan</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.vehicle_type} onChange={e => setData('vehicle_type', e.target.value)} placeholder="Contoh: CDE, CDD, Fuso, Tronton" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Tahun Pembuatan</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="number" className="kt-input w-full" value={data.year} onChange={e => setData('year', e.target.value)} placeholder="YYYY" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Warna</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.color} onChange={e => setData('color', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Status Kepemilikan</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.ownership} onChange={e => setData('ownership', e.target.value)} placeholder="Contoh: Milik Sendiri, Sewa" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4">Status Kendaraan *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4">
                                                <select className="kt-select w-full" value={data.status} onChange={e => setData('status', e.target.value)} required>
                                                    <option value="active">Aktif</option>
                                                    <option value="inactive">Nonaktif / Rusak</option>
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
                                <h3 className="kt-card-title text-base font-bold">Kapasitas & Perizinan</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Max Beban (Kg)</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="number" className="kt-input w-full" value={data.max_capacity} onChange={e => setData('max_capacity', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Max Volume (CBM)</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="number" step="0.01" className="kt-input w-full" value={data.max_volume} onChange={e => setData('max_volume', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">No. STNK</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.stnk_number} onChange={e => setData('stnk_number', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Berlaku STNK</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="date" className="kt-input w-full" value={data.stnk_expiry} onChange={e => setData('stnk_expiry', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">No. KIR</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.kir_number} onChange={e => setData('kir_number', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Berlaku KIR</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="date" className="kt-input w-full" value={data.kir_expiry} onChange={e => setData('kir_expiry', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4">Catatan Kendaraan</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4">
                                                <textarea className="kt-input w-full min-h-[50px] resize-y py-2" value={data.notes} onChange={e => setData('notes', e.target.value)}></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div className="flex items-center justify-end gap-3 pt-2 mt-4">
                            <Link href="/vehicles" className="kt-btn kt-btn-outline grow md:grow-0 justify-center">
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
