import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AsyncSelect from 'react-select/async';
import Select from 'react-select';
import axios from 'axios';

export default function Form({ shipment, customers, layanans }) {
    const isEdit = !!shipment;
    
    const formatCurrency = (val) => {
        if (!val) return '0';
        const num = val.toString().replace(/[^0-9]/g, '');
        return new Intl.NumberFormat('id-ID').format(num);
    };

    const parseCurrency = (val) => {
        if (!val) return 0;
        return parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
    };

    const getCityId = (city) => (typeof city === 'object' && city ? city.id : city) || '';
    const getCityName = (city) => (typeof city === 'object' && city ? city.name : city) || '';

    const initialSenderCity = isEdit && shipment.sender_city ? { value: getCityId(shipment.sender_city), label: getCityName(shipment.sender_city) } : null;
    const initialReceiverCity = isEdit && shipment.receiver_city ? { value: getCityId(shipment.receiver_city), label: getCityName(shipment.receiver_city) } : null;

    const { data, setData, post, put, processing, errors } = useForm({
        shipment_date: shipment?.shipment_date?.substring(0, 10) || new Date().toISOString().substring(0, 10),
        awb_number: shipment?.awb_number || '',
        service_type: shipment?.service_type || '',
        customer_id: shipment?.customer_id || '',
        
        sender_name: shipment?.sender_name || '',
        sender_phone: shipment?.sender_phone || '',
        sender_city: getCityId(shipment?.sender_city),
        sender_address: shipment?.sender_address || '',
        
        receiver_name: shipment?.receiver_name || '',
        receiver_phone: shipment?.receiver_phone || '',
        receiver_city: getCityId(shipment?.receiver_city),
        receiver_address: shipment?.receiver_address || '',
        
        item_name: shipment?.item_name || '',
        koli: shipment?.koli || 1,
        measurement_type: shipment?.measurement_type || 'kg',
        weight: shipment?.weight || 0,
        volume_weight: shipment?.volume_weight || 0,
        kubik: shipment?.kubik || 0,
        
        shipping_cost: shipment?.shipping_cost ? formatCurrency(shipment.shipping_cost) : '0',
        ppn_cost: shipment?.ppn_cost ? formatCurrency(shipment.ppn_cost) : '0',
        packing_cost: shipment?.packing_cost ? formatCurrency(shipment.packing_cost) : '0',
        other_cost: shipment?.other_cost ? formatCurrency(shipment.other_cost) : '0',
        
        payment_type: shipment?.payment_type || 'cash',
        notes: shipment?.notes || ''
    });

    const [senderCityOption, setSenderCityOption] = useState(initialSenderCity);
    const [receiverCityOption, setReceiverCityOption] = useState(initialReceiverCity);
    
    // Derived total cost
    const totalCost = parseCurrency(data.shipping_cost) + parseCurrency(data.ppn_cost) + parseCurrency(data.packing_cost) + parseCurrency(data.other_cost);

    // Manual calculation logic (runs exactly like the old JQuery logic)
    const calculateCost = (overrides = {}) => {
        const currentData = { ...data, ...overrides };
        
        let val = 0;
        if (currentData.measurement_type === 'kg') val = currentData.weight;
        else if (currentData.measurement_type === 'kgv') val = currentData.volume_weight;
        else if (currentData.measurement_type === 'kubik') val = currentData.kubik;

        if (currentData.service_type && currentData.sender_city && currentData.receiver_city && currentData.measurement_type && val > 0) {
            axios.get('/shipments/calculate-cost', {
                params: {
                    service_type: currentData.service_type,
                    origin: currentData.sender_city,
                    destination: currentData.receiver_city,
                    measurement_type: currentData.measurement_type,
                    value: val
                }
            }).then(res => {
                if (res.data && parseFloat(res.data.cost) > 0) {
                    const finalCost = parseInt(res.data.cost);
                    setData('shipping_cost', formatCurrency(finalCost));
                }
            }).catch(err => console.error("Error calculating cost", err));
        }
    };

    // Auto-calculate shipping cost on dependent field changes
    useEffect(() => {
        calculateCost();
    }, [data.service_type, data.sender_city, data.receiver_city, data.measurement_type, data.weight, data.volume_weight, data.kubik]);

    // Handle Customer Selection -> Autofill Sender
    const handleCustomerChange = (selectedOption) => {
        const id = selectedOption ? selectedOption.value : '';
        setData('customer_id', id);
        
        if (id) {
            axios.get('/customers/get-customer', { params: { id } })
                .then(res => {
                    if (res.data && res.data.status === 'success' && res.data.data) {
                        const c = res.data.data;
                        const updates = {
                            sender_name: c.name || '',
                            sender_phone: c.phone || '',
                            sender_address: c.address || '',
                            customer_id: id
                        };
                        if (c.city && c.city_name) {
                            updates.sender_city = c.city;
                            setSenderCityOption({ value: c.city, label: c.city_name });
                        }
                        setData(data => ({ ...data, ...updates }));
                        
                        // Recalculate cost with the newly fetched sender city
                        if (c.city) {
                            calculateCost({ sender_city: c.city });
                        }
                    }
                });
        } else {
            setData(data => ({
                ...data,
                customer_id: '',
                sender_name: '',
                sender_phone: '',
                sender_address: '',
                sender_city: ''
            }));
            setSenderCityOption(null);
            calculateCost({ sender_city: '' });
        }
    };

    const loadCities = (inputValue, callback) => {
        if (!inputValue) {
            callback([]);
            return;
        }
        axios.get('/shipments/search-regencies', { params: { q: inputValue } })
            .then(res => {
                const options = res.data.map(item => ({ value: item.id, label: item.name }));
                callback(options);
            }).catch(() => callback([]));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        
        // Clean currency fields before submitting
        const payload = {
            ...data,
            shipping_cost: parseCurrency(data.shipping_cost),
            ppn_cost: parseCurrency(data.ppn_cost),
            packing_cost: parseCurrency(data.packing_cost),
            other_cost: parseCurrency(data.other_cost)
        };

        if (isEdit) {
            router.put(`/shipments/${shipment.id}`, payload);
        } else {
            router.post('/shipments', payload);
        }
    };

    const selectStyles = {
        control: (base, state) => ({
            ...base,
            border: state.isFocused ? '1px solid var(--primary)' : '1px solid var(--border)',
            boxShadow: state.isFocused ? '0 0 0 3px color-mix(in oklab, var(--primary) 10%, transparent)' : 'none',
            borderRadius: '6px',
            minHeight: '38px',
            background: 'var(--background)'
        }),
        menu: (base) => ({ ...base, background: 'var(--background)', border: '1px solid var(--border)', borderRadius: '6px', zIndex: 50 }),
        option: (base, state) => ({
            ...base,
            backgroundColor: state.isSelected ? 'var(--primary)' : state.isFocused ? 'var(--muted)' : 'transparent',
            color: state.isSelected ? '#fff' : 'var(--foreground)',
            cursor: 'pointer'
        }),
        singleValue: (base) => ({ ...base, color: 'var(--foreground)' })
    };

    const customerOptions = customers.map(c => ({ value: c.id, label: c.name || c.nama }));
    const selectedCustomer = customerOptions.find(o => o.value === data.customer_id) || null;

    return (
        <div className="grid gap-5 lg:gap-7.5">
            <Head title={isEdit ? 'Edit Shipment' : 'Buat Shipment Baru'} />

            {/* Error Alerts */}
            {Object.keys(errors).length > 0 && (
                <div className="kt-alert kt-alert-destructive mb-6">
                    <div className="kt-alert-content">
                        <div className="kt-alert-description">
                            <ul className="list-disc ps-4 text-sm">
                                {Object.values(errors).map((err, i) => <li key={i}>{err}</li>)}
                            </ul>
                        </div>
                    </div>
                </div>
            )}

            <form onSubmit={handleSubmit} className="w-full">
                <div className="kt-container-fixed flex flex-col gap-5 lg:gap-7.5">
                    
                    {/* DETAIL UTAMA SHIPMENT - FULL WIDTH */}
                    <div className="kt-card min-w-full">
                        <div className="kt-card-header">
                            <h3 className="kt-card-title"><i className="ki-filled ki-information text-primary mr-1"></i> Detail Utama Shipment</h3>
                        </div>
                        <div className="kt-card-table kt-scrollable-x-auto pb-3">
                            <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                <tbody>
                                    <tr>
                                        <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-calendar text-xs mr-1"></i> Tanggal Shipment *</td>
                                        <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                            <input type="date" value={data.shipment_date} onChange={e => setData('shipment_date', e.target.value)} className="kt-input w-full" required />
                                        </td>
                                        <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-hashtag text-xs mr-1"></i> No AWB *</td>
                                        <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                            <input type="text" value={data.awb_number} onChange={e => setData('awb_number', e.target.value)} className="kt-input w-full" placeholder="Masukkan No AWB" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-compass text-xs mr-1"></i> Layanan *</td>
                                        <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                            <select value={data.service_type} onChange={e => setData('service_type', e.target.value)} className="kt-select w-full" required>
                                                <option value="">-- Pilih Layanan --</option>
                                                {layanans.map(l => <option key={l.kode_layanan} value={l.kode_layanan}>{l.nama_layanan}</option>)}
                                            </select>
                                        </td>
                                        <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-user text-xs mr-1"></i> Customer</td>
                                        <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                            <Select 
                                                options={customerOptions}
                                                value={selectedCustomer}
                                                onChange={handleCustomerChange}
                                                placeholder="-- Umum / Langsung --"
                                                isClearable
                                                styles={selectStyles}
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* 2 COLUMN: PENGIRIM & PENERIMA */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-7.5">
                        {/* DATA PENGIRIM */}
                        <div className="kt-card min-w-full">
                            <div className="kt-card-header">
                                <h3 className="kt-card-title"><i className="ki-filled ki-send text-primary mr-1"></i> Data Pengirim</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-user-tick text-xs mr-1"></i> Nama Pengirim *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <input type="text" value={data.sender_name} onChange={e => setData('sender_name', e.target.value)} className="kt-input w-full" placeholder="Nama pengirim" required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-phone text-xs mr-1"></i> No HP Pengirim *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <input type="text" value={data.sender_phone} onChange={e => setData('sender_phone', e.target.value)} className="kt-input w-full" placeholder="Contoh: 0812345678" required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-building-2 text-xs mr-1"></i> Kota Asal *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <AsyncSelect 
                                                    cacheOptions defaultOptions 
                                                    loadOptions={loadCities}
                                                    value={senderCityOption}
                                                    onChange={opt => {
                                                        setSenderCityOption(opt);
                                                        setData('sender_city', opt ? opt.value : '');
                                                    }}
                                                    placeholder="-- Pilih Kota Asal --"
                                                    styles={selectStyles}
                                                    required
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-location text-xs mr-1"></i> Alamat Pengirim *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <textarea value={data.sender_address} onChange={e => setData('sender_address', e.target.value)} className="kt-input w-full min-h-[70px] resize-y" placeholder="Alamat lengkap pengirim" required></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {/* DATA PENERIMA */}
                        <div className="kt-card min-w-full">
                            <div className="kt-card-header">
                                <h3 className="kt-card-title"><i className="ki-filled ki-call text-primary mr-1"></i> Data Penerima</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-user text-xs mr-1"></i> Nama Penerima *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <input type="text" value={data.receiver_name} onChange={e => setData('receiver_name', e.target.value)} className="kt-input w-full" placeholder="Nama penerima" required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-sms text-xs mr-1"></i> No HP Penerima *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <input type="text" value={data.receiver_phone} onChange={e => setData('receiver_phone', e.target.value)} className="kt-input w-full" placeholder="Contoh: 0812345678" required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-building-2 text-xs mr-1"></i> Kota Tujuan *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <AsyncSelect 
                                                    cacheOptions defaultOptions 
                                                    loadOptions={loadCities}
                                                    value={receiverCityOption}
                                                    onChange={opt => {
                                                        setReceiverCityOption(opt);
                                                        setData('receiver_city', opt ? opt.value : '');
                                                    }}
                                                    placeholder="-- Pilih Kota Tujuan --"
                                                    styles={selectStyles}
                                                    required
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-map text-xs mr-1"></i> Alamat Penerima *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <textarea value={data.receiver_address} onChange={e => setData('receiver_address', e.target.value)} className="kt-input w-full min-h-[70px] resize-y" placeholder="Alamat lengkap penerima" required></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {/* 2 COLUMN: BARANG & BIAYA */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-7.5">
                        {/* DETAIL BARANG */}
                        <div className="kt-card min-w-full">
                            <div className="kt-card-header">
                                <h3 className="kt-card-title"><i className="ki-filled ki-box text-primary mr-1"></i> Detail Barang</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-package text-xs mr-1"></i> Nama Barang *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <input type="text" value={data.item_name} onChange={e => setData('item_name', e.target.value)} className="kt-input w-full" placeholder="Nama barang" required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-cube text-xs mr-1"></i> Jumlah Koli *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <input type="number" value={data.koli} onChange={e => setData('koli', parseInt(e.target.value) || 0)} className="kt-input w-full" placeholder="Jumlah koli" required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-measurement-tape text-xs mr-1"></i> Tipe Pengukuran *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <div className="flex items-center gap-5">
                                                    {['kg', 'kgv', 'kubik'].map(type => (
                                                        <label key={type} className="flex items-center gap-2 cursor-pointer">
                                                            <input type="radio" name="measurement_type" value={type} className="kt-radio" 
                                                                checked={data.measurement_type === type} 
                                                                onChange={e => setData('measurement_type', e.target.value)} 
                                                                required 
                                                            />
                                                            <span className="text-sm font-medium capitalize">{type === 'kgv' ? 'Kgv' : type}</span>
                                                        </label>
                                                    ))}
                                                </div>
                                            </td>
                                        </tr>
                                        {data.measurement_type === 'kg' && (
                                            <tr>
                                                <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-weight text-xs mr-1"></i> Berat (Kg) *</td>
                                                <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                    <input type="number" step="0.01" value={data.weight} onChange={e => setData('weight', parseFloat(e.target.value) || 0)} className="kt-input w-full" placeholder="Berat aktual" required />
                                                </td>
                                            </tr>
                                        )}
                                        {data.measurement_type === 'kgv' && (
                                            <tr>
                                                <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-balancer text-xs mr-1"></i> Volume (Kgv) *</td>
                                                <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                    <input type="number" step="0.01" value={data.volume_weight} onChange={e => setData('volume_weight', parseFloat(e.target.value) || 0)} className="kt-input w-full" placeholder="Volume weight" required />
                                                </td>
                                            </tr>
                                        )}
                                        {data.measurement_type === 'kubik' && (
                                            <tr>
                                                <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-cube text-xs mr-1"></i> Kubik *</td>
                                                <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                    <input type="number" step="0.01" value={data.kubik} onChange={e => setData('kubik', parseFloat(e.target.value) || 0)} className="kt-input w-full" placeholder="Volume kubik" required />
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {/* BIAYA PENGIRIMAN */}
                        <div className="kt-card min-w-full">
                            <div className="kt-card-header">
                                <h3 className="kt-card-title"><i className="ki-filled ki-dollar text-primary mr-1"></i> Biaya Pengiriman</h3>
                            </div>
                            <div className="kt-card-table kt-scrollable-x-auto pb-3">
                                <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                    <tbody>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-money-send text-xs mr-1"></i> Biaya Kirim (Rp) *</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <input type="text" value={data.shipping_cost} onChange={e => setData('shipping_cost', formatCurrency(e.target.value))} className="kt-input w-full currency-input" placeholder="0" required />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-receipt text-xs mr-1"></i> Biaya PPN (Rp)</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <input type="text" value={data.ppn_cost} onChange={e => setData('ppn_cost', formatCurrency(e.target.value))} className="kt-input w-full currency-input" placeholder="0" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-box text-xs mr-1"></i> Biaya Packing (Rp)</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <input type="text" value={data.packing_cost} onChange={e => setData('packing_cost', formatCurrency(e.target.value))} className="kt-input w-full currency-input" placeholder="0" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-notepad text-xs mr-1"></i> Biaya Lain (Rp)</td>
                                            <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                                <input type="text" value={data.other_cost} onChange={e => setData('other_cost', formatCurrency(e.target.value))} className="kt-input w-full currency-input" placeholder="0" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colSpan="2" className="py-2.5 sm:py-3 px-2 sm:px-4">
                                                <div id="total_tagihan_box" className="p-4 rounded-2xl flex flex-col gap-1 w-full" style={{ background: 'linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%)', border: '1px solid #6ee7b7' }}>
                                                    <label className="text-[10px] font-extrabold text-emerald-800 uppercase tracking-widest">TOTAL TAGIHAN</label>
                                                    <div className="flex items-center gap-1">
                                                        <span className="text-lg font-bold text-emerald-600">Rp</span>
                                                        <span className="text-emerald-600 font-extrabold text-2xl">
                                                            {totalCost.toLocaleString('id-ID')}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {/* TIPE PEMBAYARAN */}
                    <div className="kt-card min-w-full">
                        <div className="kt-card-header">
                            <h3 className="kt-card-title"><i className="ki-filled ki-wallet text-primary mr-1"></i> Tipe Pembayaran</h3>
                        </div>
                        <div className="kt-card-table kt-scrollable-x-auto pb-3">
                            <table className="kt-table align-middle text-sm text-muted-foreground w-full">
                                <tbody>
                                    <tr>
                                        <td className="w-24 sm:w-36 lg:w-44 text-secondary-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4"><i className="ki-filled ki-wallet text-xs mr-1"></i> Tipe Pembayaran *</td>
                                        <td className="text-foreground font-normal py-2.5 sm:py-3 px-2 sm:px-4">
                                            <select value={data.payment_type} onChange={e => setData('payment_type', e.target.value)} className="kt-select w-full" required>
                                                <option value="cash">Cash</option>
                                                <option value="transfer">Transfer</option>
                                                <option value="invoice">Invoice</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* CATATAN */}
                    <div className="kt-card min-w-full">
                        <div className="kt-card-header">
                            <h3 className="kt-card-title"><i className="ki-filled ki-notepad text-primary mr-1"></i> Catatan</h3>
                        </div>
                        <div className="kt-card-body p-5">
                            <textarea value={data.notes} onChange={e => setData('notes', e.target.value)} className="kt-input w-full min-h-[80px] resize-y" placeholder="Catatan pengiriman (opsional)"></textarea>
                        </div>
                    </div>

                    {/* BUTTONS */}
                    <div className="flex items-center justify-end gap-3 pt-2">
                        <Link href="/shipments" className="kt-btn kt-btn-outline grow md:grow-0 justify-center">
                            Batal
                        </Link>
                        <button type="submit" disabled={processing} className="kt-btn kt-btn-primary grow md:grow-0 justify-center">
                            {processing ? <i className="ki-filled ki-spinner-record animate-spin text-sm"></i> : <i className="ki-filled ki-check text-sm"></i>} 
                            {isEdit ? 'Simpan Perubahan' : 'Simpan'}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    );
}
