<?php

namespace Modules\Olahdata\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OlahdataController extends Controller
{
    public function index()
    {
        $projects = [
            [
                'id' => 101,
                'judul' => 'Analisis Jalur Pengaruh Brand Equity Terhadap Loyalitas Pelanggan',
                'metode' => 'SEM-PLS (SmartPLS 4)',
                'software' => 'SmartPLS',
                'dataset' => 'dataset_responden_240.xlsx',
                'progress' => 85,
                'status' => 'Penyusunan Bab 4',
                'analis' => 'Nur Atika Rochmah, M.Si.',
                'updated_at' => '07 Okt 2026, 09:30 WIB'
            ],
            [
                'id' => 102,
                'judul' => 'Model Regresi Data Panel Determinan Profitabilitas Bank BUKU IV',
                'metode' => 'Regresi Data Panel (Fixed Effect Model)',
                'software' => 'EViews 12',
                'dataset' => 'data_keuangan_perbankan_2018_2025.csv',
                'progress' => 100,
                'status' => 'Selesai & ACC',
                'analis' => 'Deny Aditya Pratama, S.Stat.',
                'updated_at' => '06 Okt 2026, 17:00 WIB'
            ],
            [
                'id' => 103,
                'judul' => 'Uji Beda Pengaruh Pembelajaran Berbasis AI Terhadap Literasi Sains',
                'metode' => 'Independent Samples t-Test & ANCOVA',
                'software' => 'SPSS 29',
                'dataset' => 'nilai_pretest_posttest.sav',
                'progress' => 45,
                'status' => 'Uji Asumsi Klasik',
                'analis' => 'Arrum Rochmani Hidayah, M.Kom.',
                'updated_at' => '05 Okt 2026, 11:20 WIB'
            ]
        ];

        return view('olahdata::index', compact('projects'));
    }

    public function create()
    {
        return view('olahdata::create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_riset' => 'required|string|max:255',
            'metode_analisis' => 'required|string',
            'software' => 'required|string',
            'dataset' => 'nullable|file|max:10240',
        ]);

        return redirect()->route('olahdata.index')->with('success', 'Project olah data berhasil diajukan. Tim analis akan memproses dataset Anda!');
    }

    public function diagnosis()
    {
        return view('olahdata::diagnosis');
    }

    public function show($id)
    {
        return view('olahdata::show', compact('id'));
    }
}
