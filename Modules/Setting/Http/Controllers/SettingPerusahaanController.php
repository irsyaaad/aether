<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Modules\Setting\Entities\SettingPerusahaan;

class SettingPerusahaanController extends Controller
{
    /**
     * Show the form for editing the company settings.
     * @return Renderable
     */
    public function edit()
    {
        // Get the first record or instantiate a new one if empty
        $setting = SettingPerusahaan::first();
        
        if (!$setting) {
            $setting = SettingPerusahaan::create([
                'nama_perusahaan' => 'PT Putera Adhi Perkasa',
            ]);
        }

        return view('setting::perusahaan', compact('setting'));
    }

    /**
     * Update the company settings.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $setting = SettingPerusahaan::first();
        
        if (!$setting) {
            $setting = new SettingPerusahaan();
        }

        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'pemilik'         => 'required|string|max:255',
            'alamat'          => 'nullable|string',
            'kota'            => 'nullable|string|max:255',
            'telepon'         => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:255',
            'website'         => 'nullable|string|max:255',
            'npwp'            => 'nullable|string|max:50',
            'visi_misi'       => 'nullable|string',
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'keterangan'      => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo) {
                Storage::disk('public')->delete($setting->logo);
            }
            // Store new logo
            $validated['logo'] = $request->file('logo')->store('logo', 'public');
        }

        $validated['updated_by'] = Auth::id();

        $setting->fill($validated);
        $setting->save();

        return redirect()->back()->with('success', 'Konfigurasi perusahaan berhasil disimpan.');
    }
}
