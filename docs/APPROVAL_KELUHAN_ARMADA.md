# Dokumentasi: Approval Keluhan Armada

## Deskripsi
Fitur approval keluhan armada memungkinkan user untuk menyetujui keluhan dengan menentukan kategori keluhan dan tindak lanjut yang akan dilakukan.

## Alur Proses

### 1. Status Approval
- **Pending**: Status default saat keluhan dibuat
- **Approved**: Keluhan telah di-approve dengan kategori dan tindak lanjut
- **Rejected**: Keluhan ditolak (future feature)

### 2. Kategori Keluhan
Saat approval, user harus memilih kategori:
1. **Darurat** - Keluhan yang memerlukan penanganan segera
2. **Sedang** - Keluhan dengan prioritas menengah
3. **Minor** - Keluhan dengan prioritas rendah

### 3. Pilihan Tindak Lanjut

#### a. Buat SPK Baru
- Sistem otomatis membuat SPK (Surat Perintah Kerja) baru
- SPK dibuat dengan status "Open"
- Keluhan otomatis berubah status menjadi "In Progress"
- SPK terhubung dengan keluhan armada

#### b. Gabung dengan SPK yang Status Open
- User memilih SPK yang sudah ada dengan status "Open"
- Keluhan akan digabungkan dengan SPK tersebut
- Keluhan otomatis berubah status menjadi "In Progress"
- Hanya menampilkan SPK dengan status Open untuk armada yang sama

#### c. Langsung Proses/Close
- Keluhan langsung ditutup tanpa membuat SPK
- User harus mengisi:
  - **Keterangan Rekomendasi**: Penjelasan mengapa langsung di-close
  - **Tanggal Deadline**: Target penyelesaian
- Keluhan otomatis berubah status menjadi "Done"

## Cara Penggunaan

### 1. Approve Keluhan

1. Buka detail keluhan armada
2. Klik tombol **"Approve Keluhan"** (hanya muncul jika status approval = pending)
3. Modal approval akan muncul dengan form:
   - Pilih **Kategori Keluhan** (Darurat/Sedang/Minor)
   - Pilih **Tindak Lanjut** (Buat SPK/Gabung SPK/Langsung Close)
4. Isi field tambahan sesuai pilihan tindak lanjut
5. Klik **"Approve"**

### 2. Field Conditional

**Jika pilih "Gabung dengan SPK yang Status Open":**
- Muncul dropdown untuk memilih SPK
- Dropdown otomatis load SPK yang status Open untuk armada yang sama

**Jika pilih "Langsung Proses/Close":**
- Muncul textarea untuk Keterangan Rekomendasi
- Muncul input date untuk Tanggal Deadline

## Database Schema

### Tabel: keluhan_armada

Field baru yang ditambahkan:
```sql
kategori_keluhan ENUM('darurat', 'sedang', 'minor') NULL
status_approval ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
keterangan_approval TEXT NULL
tanggal_deadline DATE NULL
approved_by INT NULL
approved_at TIMESTAMP NULL
```

### Tabel: keluhan_tindak_lanjut

Field baru yang ditambahkan:
```sql
status ENUM('open', 'in_progress', 'closed') DEFAULT 'open'
```

## API Endpoints

### 1. Approve Keluhan
```
POST /keluhan-armada/{id}/approve
```

**Request Body:**
```json
{
  "kategori_keluhan": "darurat",
  "tindak_lanjut": "buat_spk"
}
```

**Response Success:**
```json
{
  "status": "success",
  "message": "Keluhan berhasil di-approve"
}
```

### 2. Get Open SPK
```
GET /keluhan-armada/get-open-spk/{armadaId?}
```

**Response:**
```json
[
  {
    "id": 1,
    "text": "SPK #1 - SPK/SDA/2602/001 (2026-02-10)"
  }
]
```

## Migration

Jalankan migration untuk menambahkan field baru:

```bash
php artisan migrate
```

File migration:
- `2026_02_10_000001_add_approval_fields_to_keluhan_armada_table.php`
- `2026_02_10_000002_add_status_to_keluhan_tindak_lanjut_table.php`

## Validasi

### Approval Request
- `kategori_keluhan`: required, in:darurat,sedang,minor
- `tindak_lanjut`: required, in:buat_spk,gabung_spk,langsung_close
- `spk_id`: required_if:tindak_lanjut,gabung_spk
- `keterangan_approval`: required_if:tindak_lanjut,langsung_close
- `tanggal_deadline`: required_if:tindak_lanjut,langsung_close, date

## Audit Log

Setiap approval akan tercatat di audit log dengan informasi:
- Action: update
- Table: keluhan_armada
- Old data: data sebelum approval
- New data: data setelah approval
- User: user yang melakukan approval

## UI/UX

### Tombol Approve
- Hanya muncul jika `status_approval = 'pending'`
- Warna hijau dengan icon check
- Posisi di header detail keluhan

### Badge Status
- **Pending**: Badge kuning
- **Approved**: Badge hijau
- **Rejected**: Badge merah

### Badge Kategori
- **Darurat**: Badge merah
- **Sedang**: Badge kuning
- **Minor**: Badge biru

## Troubleshooting

**Q: Tombol approve tidak muncul**
A: Pastikan status_approval keluhan masih "pending". Jika sudah approved, tombol tidak akan muncul.

**Q: Dropdown SPK kosong**
A: Pastikan ada SPK dengan status "open" untuk armada yang sama. Jika tidak ada, pilih opsi "Buat SPK Baru".

**Q: Error saat approve**
A: Cek validasi form. Pastikan semua field required sudah diisi sesuai pilihan tindak lanjut.

## Future Enhancement

1. Fitur reject keluhan dengan alasan
2. Notifikasi email/WhatsApp saat keluhan di-approve
3. Dashboard monitoring keluhan berdasarkan kategori
4. Auto-assign teknisi berdasarkan kategori keluhan
5. SLA tracking berdasarkan kategori dan deadline
