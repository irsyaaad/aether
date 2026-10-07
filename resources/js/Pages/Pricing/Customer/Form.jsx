import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AsyncSelect from 'react-select/async';
import axios from 'axios';
import Select from 'react-select';

export default function Form({ customerPricing, customers, layanans }) {
    const isEdit = !!customerPricing;
    
    // Format dates
    const formatDate = (dateString) => {
        if (!dateString) return '';
        return dateString.substring(0, 10);
    };

    // Initial Option for React Select if editing
    const [originOption, setOriginOption] = useState(
        isEdit && customerPricing.origin_regency 
            ? { value: customerPricing.origin_city, label: customerPricing.origin_regency.name } 
            : null
    );
    const [destinationOption, setDestinationOption] = useState(
        isEdit && customerPricing.destination_regency 
            ? { value: customerPricing.destination_city, label: customerPricing.destination_regency.name } 
            : null
    );
    
    const [customerOption, setCustomerOption] = useState(
        isEdit && customerPricing.customer 
            ? { value: customerPricing.customer_id, label: customerPricing.customer.name } 
            : null
    );

    const { data, setData, post, put, processing, errors } = useForm({
        customer_id: customerPricing?.customer_id || '',
        service_type: customerPricing?.service_type || '',
        origin_city: customerPricing?.origin_city || '',
        destination_city: customerPricing?.destination_city || '',
        price_per_kg: customerPricing?.price_per_kg || '',
        min_charge: customerPricing?.min_charge || '',
        fuel_surcharge: customerPricing?.fuel_surcharge || '',
        estimation_day: customerPricing?.estimation_day || '',
        valid_from: formatDate(customerPricing?.valid_from),
        valid_until: formatDate(customerPricing?.valid_until),
        status: customerPricing?.status || 'active',
        notes: customerPricing?.notes || ''
    });

    const loadRegencies = (inputValue, callback) => {
        if (!inputValue) {
            callback([]);
            return;
        }
        
        axios.get('/city-pricing/search-regencies', { params: { q: inputValue } })
            .then(res => {
                const options = res.data.map(item => ({
                    value: item.id,
                    label: item.name
                }));
                callback(options);
            })
            .catch(err => {
                console.error(err);
                callback([]);
            });
    };

    const handleOriginChange = (selected) => {
        setOriginOption(selected);
        setData('origin_city', selected ? selected.value : '');
    };

    const handleDestinationChange = (selected) => {
        setDestinationOption(selected);
        setData('destination_city', selected ? selected.value : '');
    };

    const handleCustomerChange = (selected) => {
        setCustomerOption(selected);
        setData('customer_id', selected ? selected.value : '');
    };

    const customSelectStyles = {
        control: (provided, state) => ({
            ...provided,
            borderColor: state.isFocused ? 'var(--tw-colors-primary)' : 'var(--tw-colors-border)',
            boxShadow: state.isFocused ? '0 0 0 1px var(--tw-colors-primary)' : 'none',
            borderRadius: '0.375rem',
            padding: '2px',
            backgroundColor: 'var(--tw-colors-background)',
        }),
        menuPortal: base => ({ ...base, zIndex: 9999 })
    };

    const customerOptions = customers.map(c => ({
        value: c.id,
        label: c.name
    }));

    const handleSubmit = (e) => {
        e.preventDefault();
        
        const options = {
            onSuccess: (page) => {
                if (page.props.flash && page.props.flash.error) {
                    if (typeof window !== 'undefined' && window.Swal) {
                        window.Swal.fire({
                            title: "Gagal!",
                            text: page.props.flash.error,
                            icon: "error",
                            confirmButtonText: "Tutup"
                        });
                    }
                } else {
                    if (typeof window !== 'undefined' && window.Swal) {
                        window.Swal.fire({
                            title: "Berhasil!",
                            text: isEdit ? "Harga customer berhasil diubah." : "Harga customer baru berhasil ditambahkan.",
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            }
        };

        if (isEdit) {
            put(`/customer-pricing/${customerPricing.id}`, options);
        } else {
            post('/customer-pricing', options);
        }
    };

    return (
        <div className="w-full pb-10">
            <Head title={isEdit ? 'Edit Customer Pricing' : 'Tambah Customer Pricing'} />
            
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h2 className="text-xl font-bold text-foreground">{isEdit ? 'Ubah Harga Customer' : 'Tambah Harga Customer Baru'}</h2>
                </div>
                <Link href="/customer-pricing" className="kt-btn kt-btn-outline">
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
                                <h3 className="kt-card-title text-base font-bold">Informasi Rute & Layanan</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Customer *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border" style={{ position: 'relative' }}>
                                                <Select 
                                                    options={customerOptions}
                                                    value={customerOption}
                                                    onChange={handleCustomerChange}
                                                    placeholder="Pilih Customer..."
                                                    styles={customSelectStyles}
                                                    menuPortalTarget={typeof document !== 'undefined' ? document.body : null}
                                                    menuPosition="fixed"
                                                    isClearable
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Layanan *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <select className="kt-select w-full" value={data.service_type} onChange={e => setData('service_type', e.target.value)} required>
                                                    <option value="">Pilih Layanan</option>
                                                    {layanans.map(l => (
                                                        <option key={l.kode_layanan} value={l.kode_layanan}>{l.nama_layanan}</option>
                                                    ))}
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Kota Asal *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border" style={{ position: 'relative' }}>
                                                <AsyncSelect 
                                                    cacheOptions 
                                                    defaultOptions 
                                                    loadOptions={loadRegencies}
                                                    value={originOption}
                                                    onChange={handleOriginChange}
                                                    placeholder="Ketik nama kota asal..."
                                                    styles={customSelectStyles}
                                                    menuPortalTarget={typeof document !== 'undefined' ? document.body : null}
                                                    menuPosition="fixed"
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Kota Tujuan *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border" style={{ position: 'relative' }}>
                                                <AsyncSelect 
                                                    cacheOptions 
                                                    defaultOptions 
                                                    loadOptions={loadRegencies}
                                                    value={destinationOption}
                                                    onChange={handleDestinationChange}
                                                    placeholder="Ketik nama kota tujuan..."
                                                    styles={customSelectStyles}
                                                    menuPortalTarget={typeof document !== 'undefined' ? document.body : null}
                                                    menuPosition="fixed"
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Status *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <select className="kt-select w-full" value={data.status} onChange={e => setData('status', e.target.value)} required>
                                                    <option value="active">Aktif</option>
                                                    <option value="inactive">Nonaktif</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4">Keterangan</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4">
                                                <textarea className="kt-input w-full min-h-[50px] resize-y py-2" value={data.notes} onChange={e => setData('notes', e.target.value)}></textarea>
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
                                <h3 className="kt-card-title text-base font-bold">Detail Harga Dasar & Masa Berlaku</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Harga / Kg *</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <div className="relative">
                                                    <span className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">Rp</span>
                                                    <input 
                                                        type="text" 
                                                        className="kt-input w-full pl-9" 
                                                        value={data.price_per_kg ? String(data.price_per_kg).replace(/\B(?=(\d{3})+(?!\d))/g, ".") : ''} 
                                                        onChange={e => setData('price_per_kg', e.target.value.replace(/\D/g, ''))} 
                                                        required 
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Minimum Charge</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <div className="relative">
                                                    <span className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">Rp</span>
                                                    <input 
                                                        type="text" 
                                                        className="kt-input w-full pl-9" 
                                                        value={data.min_charge ? String(data.min_charge).replace(/\B(?=(\d{3})+(?!\d))/g, ".") : ''} 
                                                        onChange={e => setData('min_charge', e.target.value.replace(/\D/g, ''))} 
                                                        placeholder="Opsional" 
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Fuel Surcharge (%)</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <div className="relative">
                                                    <input type="number" step="0.01" className="kt-input w-full pr-8" value={data.fuel_surcharge} onChange={e => setData('fuel_surcharge', e.target.value)} placeholder="Opsional" />
                                                    <span className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground">%</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Berlaku Dari</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="date" className="kt-input w-full" value={data.valid_from} onChange={e => setData('valid_from', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">Berlaku Sampai</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4 border-b border-border">
                                                <input type="date" className="kt-input w-full" value={data.valid_until} onChange={e => setData('valid_until', e.target.value)} />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-32 sm:w-40 text-secondary-foreground font-normal py-2.5 px-2 sm:px-4">Estimasi Waktu</td>
                                            <td className="text-foreground font-normal py-2.5 px-2 sm:px-4">
                                                <div className="flex items-center gap-2">
                                                    <input type="number" className="kt-input w-24" value={data.estimation_day} onChange={e => setData('estimation_day', e.target.value)} placeholder="0" />
                                                    <span className="text-muted-foreground">Hari</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div className="flex items-center justify-end gap-3 pt-2 mt-4">
                            <Link href="/customer-pricing" className="kt-btn kt-btn-outline grow md:grow-0 justify-center">
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
