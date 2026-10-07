<style>
    .cv-container {
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        position: relative;
    }

    .cv-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 20px;
    }

    .cv-title-section h3 {
        margin: 0;
        font-weight: 700;
        color: #1e293b;
        font-size: 24px;
    }

    .cv-title-section p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 16px;
        font-weight: 500;
    }

    .cv-photo-container {
        width: 120px;
        height: 150px;
        border-radius: 8px;
        overflow: hidden;
        border: 3px solid #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cv-photo-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cv-photo-container i {
        font-size: 40px;
        color: #cbd5e1;
    }

    .cv-section {
        margin-bottom: 25px;
    }

    .cv-section-title {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6366f1;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cv-section-title::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    .cv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px 30px;
    }

    .cv-item {
        display: flex;
        flex-direction: column;
    }

    .cv-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 2px;
    }

    .cv-value {
        font-size: 14px;
        color: #1e293b;
        font-weight: 600;
    }

    .cv-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
    }

    .cv-badge-aktif {
        background: #dcfce7;
        color: #15803d;
    }

    .cv-badge-nonaktif {
        background: #fee2e2;
        color: #b91c1c;
    }

    @media (max-width: 640px) {
        .cv-header {
            flex-direction: column-reverse;
            align-items: center;
            text-align: center;
            gap: 20px;
        }

        .cv-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="cv-container">
    <!-- Header Section -->
    <div class="cv-header">
        <div class="cv-title-section">
            <h3>{{ $karyawan->nama_lengkap }}</h3>
            <p>{{ $karyawan->jabatan ?? 'Karyawan' }} | PT Putera Adhi Perkasa</p>
            <div class="mt-2">
                <span class="cv-badge {{ $karyawan->status == 'aktif' ? 'cv-badge-aktif' : 'cv-badge-nonaktif' }}">
                    {{ strtoupper($karyawan->status) }}
                </span>
            </div>
        </div>
        <div class="cv-photo-container">
            @if ($karyawan->foto_diri)
                <img src="{{ asset('storage/' . $karyawan->foto_diri) }}" alt="Foto Karyawan">
            @else
                <i class="fas fa-user"></i>
            @endif
        </div>
    </div>

    <!-- Personal Data Section -->
    <div class="cv-section">
        <div class="cv-section-title"><i class="fas fa-user"></i> DATA PRIBADI</div>
        <div class="cv-grid">
            <div class="cv-item">
                <span class="cv-label">Nama Panggilan</span>
                <span class="cv-value">{{ $karyawan->nama_panggilan ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">NIK KTP</span>
                <span class="cv-value">{{ $karyawan->nik_ktp ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Tempat, Tanggal Lahir</span>
                <span class="cv-value">{{ $karyawan->tempat_lahir ?? '-' }},
                    {{ $karyawan->tanggal_lahir ? dateindo($karyawan->tanggal_lahir) : '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Jenis Kelamin / Agama</span>
                <span class="cv-value">{{ $karyawan->jenis_kelamin ?? '-' }} / {{ $karyawan->agama ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Status Pernikahan</span>
                <span class="cv-value">{{ $karyawan->status_pernikahan ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Golongan Darah</span>
                <span class="cv-value">{{ $karyawan->golongan_darah ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Jenis SIM</span>
                <span class="cv-value">{{ $karyawan->jenis_sim ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Contact & Address Section -->
    <div class="cv-section">
        <div class="cv-section-title"><i class="fas fa-address-book"></i> KONTAK & ALAMAT</div>
        <div class="cv-grid">
            <div class="cv-item">
                <span class="cv-label">Nomor HP</span>
                <span class="cv-value">{{ $karyawan->nomor_hp ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Alamat Email</span>
                <span class="cv-value">{{ $karyawan->email ?? '-' }}</span>
            </div>
            <div class="cv-item" style="grid-column: 1 / -1;">
                <span class="cv-label">Alamat KTP</span>
                <span class="cv-value">{{ $karyawan->alamat_ktp ?? '-' }}</span>
            </div>
            <div class="cv-item" style="grid-column: 1 / -1;">
                <span class="cv-label">Alamat Domisili</span>
                <span class="cv-value">{{ $karyawan->alamat_domisili ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Financial & Employment Section -->
    <div class="cv-section">
        <div class="cv-section-title"><i class="fas fa-briefcase"></i> KEUANGAN & PEKERJAAN</div>
        <div class="cv-grid">
            <div class="cv-item">
                <span class="cv-label">Gaji Pokok</span>
                <span class="cv-value">Rp. {{ tonumberdown($karyawan->gaji_pokok ?? 0) }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Tunjangan Jabatan</span>
                <span class="cv-value">Rp. {{ tonumberdown($karyawan->tunjangan_jabatan ?? 0) }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Tunjangan Makan</span>
                <span class="cv-value">Rp. {{ tonumberdown($karyawan->tunjangan_makan ?? 0) }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Tunjangan Transport</span>
                <span class="cv-value">Rp. {{ tonumberdown($karyawan->tunjangan_transport ?? 0) }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Nomor Rekening ({{ $karyawan->nama_bank ?? '-' }})</span>
                <span class="cv-value">{{ $karyawan->nomor_rekening ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">NPWP / SIM</span>
                <span class="cv-value">{{ $karyawan->nomor_npwp ?? '-' }} / {{ $karyawan->nomor_sim ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">BPJS Kesehatan</span>
                <span class="cv-value">{{ $karyawan->nomor_bpjs_kesehatan ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">BPJS Ketenagakerjaan</span>
                <span class="cv-value">{{ $karyawan->nomor_bpjs_ketenagakerjaan ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">ID SPX</span>
                <span class="cv-value">{{ $karyawan->id_spx ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Emergency Contact Section -->
    <div class="cv-section">
        <div class="cv-section-title"><i class="fas fa-exclamation-triangle"></i> KONTAK DARURAT</div>
        <div class="cv-grid">
            <div class="cv-item">
                <span class="cv-label">Nama Kontak</span>
                <span class="cv-value">{{ $karyawan->kontak_darurat_nama ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Nomor HP</span>
                <span class="cv-value">{{ $karyawan->kontak_darurat_nomor ?? '-' }}</span>
            </div>
            <div class="cv-item">
                <span class="cv-label">Hubungan</span>
                <span class="cv-value">{{ $karyawan->kontak_darurat_hubungan ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>
