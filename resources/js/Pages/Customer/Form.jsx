import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import axios from 'axios';
import AsyncSelect from 'react-select/async';

export default function Form({ customer, provinces }) {
    const isEdit = !!customer;
    
    const { data, setData, post, put, processing, errors } = useForm({
        code: customer?.code || '(Auto)',
        name: customer?.name || '',
        phone: customer?.phone || '',
        email: customer?.email || '',
        address: customer?.address || '',
        province: customer?.province || '',
        city: customer?.city || '',
        pic_name: customer?.pic_name || '',
        pic_phone: customer?.pic_phone || '',
        tax_id: customer?.tax_id || '',
        postal_code: customer?.postal_code || '',
        type: customer?.type || 'regular',
        status: customer?.status || 'active',
        credit_limit: customer?.credit_limit || 0,
        credit_term: customer?.credit_term || 0,
        notes: customer?.notes || ''
    });

    // Custom Styles for react-select to match Metronic theme
    const selectStyles = {
        control: (base, state) => ({
            ...base,
            borderColor: state.isFocused ? 'var(--primary)' : 'var(--border)',
            boxShadow: state.isFocused ? '0 0 0 3px color-mix(in oklab, var(--primary) 10%, transparent)' : 'none',
            borderRadius: '6px',
            minHeight: '38px',
            backgroundColor: 'var(--background)',
            '&:hover': {
                borderColor: state.isFocused ? 'var(--primary)' : 'var(--border)'
            }
        }),
        singleValue: (base) => ({
            ...base,
            color: 'var(--foreground)'
        }),
        input: (base) => ({
            ...base,
            color: 'var(--foreground)'
        }),
        menu: (base) => ({
            ...base,
            backgroundColor: 'var(--background)',
            border: '1px solid var(--border)',
            borderRadius: '8px',
            boxShadow: '0 4px 16px rgba(0,0,0,0.08)',
            zIndex: 50
        }),
        option: (base, state) => ({
            ...base,
            backgroundColor: state.isSelected 
                ? 'var(--primary)' 
                : state.isFocused 
                    ? 'color-mix(in oklab, var(--primary) 10%, transparent)' 
                    : 'transparent',
            color: state.isSelected ? '#fff' : 'var(--foreground)',
            cursor: 'pointer',
            padding: '8px 12px',
            fontSize: '0.875rem',
            '&:active': {
                backgroundColor: 'var(--primary)'
            }
        }),
        placeholder: (base) => ({
            ...base,
            color: 'var(--muted-foreground)',
            fontSize: '0.875rem'
        }),
        indicatorSeparator: () => ({ display: 'none' })
    };

    // Load Provinces for AsyncSelect
    const loadProvinces = (inputValue, callback) => {
        axios.get(`/customers/search-provinces?q=${inputValue}`)
            .then(res => {
                const options = res.data.map(p => ({ value: p.id, label: p.name }));
                callback(options);
            })
            .catch(err => {
                console.error(err);
                callback([]);
            });
    };

    // Load Regencies (Cities) for AsyncSelect based on selected Province
    const loadRegencies = (inputValue, callback) => {
        if (!data.province) {
            callback([]);
            return;
        }
        axios.get(`/customers/search-regencies?province=${data.province}&q=${inputValue}`)
            .then(res => {
                const options = res.data.map(r => ({ value: r.id, label: r.name }));
                callback(options);
            })
            .catch(err => {
                console.error(err);
                callback([]);
            });
    };

    // Default value state for AsyncSelect in Edit mode
    const [defaultProvince, setDefaultProvince] = useState(null);
    const [defaultCity, setDefaultCity] = useState(null);

    // Set initial values if Editing (we find the name from the `provinces` array passed from backend)
    useEffect(() => {
        if (isEdit && data.province) {
            // Find province name from the initial list
            const prov = provinces.find(p => p.id == data.province);
            if (prov) {
                setDefaultProvince({ value: prov.id, label: prov.name });
            }
            
            // For city, we just fetch it once to get the label
            if (data.city) {
                axios.get(`/customers/search-regencies?province=${data.province}`)
                    .then(res => {
                        const city = res.data.find(c => c.id == data.city);
                        if (city) {
                            setDefaultCity({ value: city.id, label: city.name });
                        }
                    });
            }
        }
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        
        const options = {
            onSuccess: () => {
                if (typeof window !== 'undefined' && window.Swal) {
                    window.Swal.fire({
                        title: "Berhasil!",
                        text: isEdit ? "Data customer berhasil diubah." : "Customer baru berhasil ditambahkan.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }
        };

        if (isEdit) {
            put(`/customers/${customer.id}`, options);
        } else {
            post('/customers', options);
        }
    };

    return (
        <div className="w-full pb-10">
            <Head title={isEdit ? 'Edit Customer' : 'Tambah Customer'} />
            
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h2 className="text-xl font-bold text-foreground">{isEdit ? 'Ubah Data Customer' : 'Tambah Customer Baru'}</h2>
                </div>
                <Link href="/customers" className="kt-btn kt-btn-outline">
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
                                <h3 className="kt-card-title text-base font-bold">Informasi Customer</h3>
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
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Nama *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.name} onChange={e => setData('name', e.target.value)} required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Telepon *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.phone} onChange={e => setData('phone', e.target.value)} required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Email</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="email" className="kt-input w-full" value={data.email} onChange={e => setData('email', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Tipe *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <select className="kt-select w-full" value={data.type} onChange={e => setData('type', e.target.value)} required>
                                                    <option value="corporate">Perusahaan</option>
                                                    <option value="regular">Perorangan</option>
                                                    <option value="partner">Mitra / Partner</option>
                                                </select>
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

                        <div className="kt-card min-w-full">
                            <div className="kt-card-header">
                                <h3 className="kt-card-title text-base font-bold">Kredit & Termin</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Limit Kredit</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="number" className="kt-input w-full" value={data.credit_limit} onChange={e => setData('credit_limit', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4">Termin (Hari)</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4">
                                                <input type="number" className="kt-input w-full" value={data.credit_term} onChange={e => setData('credit_term', e.target.value)} />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {/* Kolom Kanan */}
                    <div className="flex flex-col gap-5 lg:gap-7.5">
                        <div className="kt-card min-w-full overflow-visible">
                            <div className="kt-card-header">
                                <h3 className="kt-card-title text-base font-bold">Alamat</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3 overflow-visible">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Alamat</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <textarea className="kt-input w-full min-h-[70px] resize-y py-2" value={data.address} onChange={e => setData('address', e.target.value)}></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Provinsi</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <AsyncSelect
                                                    cacheOptions
                                                    defaultOptions
                                                    loadOptions={loadProvinces}
                                                    value={defaultProvince}
                                                    onChange={(option) => {
                                                        setDefaultProvince(option);
                                                        setData('province', option ? option.value : '');
                                                        // Reset city when province changes
                                                        setDefaultCity(null);
                                                        setData('city', '');
                                                    }}
                                                    placeholder="-- Cari Provinsi --"
                                                    styles={selectStyles}
                                                    menuPortalTarget={document.body}
                                                    menuPosition="fixed"
                                                    noOptionsMessage={() => "Tidak ada data"}
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Kota</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <AsyncSelect
                                                    key={data.province} // Force remount/re-fetch defaultOptions when province changes
                                                    cacheOptions
                                                    defaultOptions={!!data.province} // Only fetch if province selected
                                                    loadOptions={loadRegencies}
                                                    value={defaultCity}
                                                    onChange={(option) => {
                                                        setDefaultCity(option);
                                                        setData('city', option ? option.value : '');
                                                    }}
                                                    placeholder={data.province ? "-- Cari Kota --" : "Pilih Provinsi Dulu"}
                                                    isDisabled={!data.province}
                                                    styles={selectStyles}
                                                    menuPortalTarget={document.body}
                                                    menuPosition="fixed"
                                                    noOptionsMessage={() => "Tidak ada data"}
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4">Kode Pos</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4">
                                                <input type="text" className="kt-input w-full" value={data.postal_code} onChange={e => setData('postal_code', e.target.value)} />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div className="kt-card min-w-full">
                            <div className="kt-card-header">
                                <h3 className="kt-card-title text-base font-bold">Kontak PIC & Pajak</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Nama PIC</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.pic_name} onChange={e => setData('pic_name', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Telepon PIC</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.pic_phone} onChange={e => setData('pic_phone', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">NPWP</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="text" className="kt-input w-full" value={data.tax_id} onChange={e => setData('tax_id', e.target.value)} />
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
                            <Link href="/customers" className="kt-btn kt-btn-outline grow md:grow-0 justify-center">
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
