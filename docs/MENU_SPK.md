# Menu SPK (Surat Perintah Kerja)

## Overview
Menu SPK adalah fitur untuk mengelola Surat Perintah Kerja yang dihasilkan dari approval keluhan armada. SPK dan Keluhan Armada adalah **dua entitas terpisah** dengan kode unik masing-masing.

## Perbedaan Keluhan Armada dan SPK

### Keluhan Armada
- **Kode Format**: `KEL/SDA/YYMM/XXX`
- **Contoh**: `KEL/SDA/2602/001` (Keluhan pertama di Februari 2026)
- **Tabel**: `keluhan_armada`
- **Generate**: Otomatis saat create keluhan baru
- **Status Approval**: pending, approved, rejected

### SPK (Surat Perintah Kerja)
- **Nomor Format**: `SPK/SDA/YYMM/XXX`
- **Contoh**: `SPK/SDA/2602/001` (SPK pertama di Februari 2026)
- **Tabel**: `keluhan_tindak_lanjut` (dengan `jenis` = 'spk')
- **Generate**: Otomatis saat approval keluhan dengan pilihan "Buat SPK Baru"
- **Status**: open, in_progress, closed

## Struktur Data

### Table: keluhan_armada
```sql
- id
- kode_keluhan (KEL/SDA/YYMM/XXX) -- Auto-generated
- armada_id
- nama_sopir
- uraian_keluhan
- kategori_keluhan (darurat, sedang, minor)
- status_approval (pending, approved, rejected)
- status (waiting, in progress, done)
- approved_by
- approved_at
```

### Table: keluhan_tindak_lanjut
```sql
- id
- nomor_spk (SPK/SDA/YYMM/XXX) -- Auto-generated jika jenis='spk'
- keluhan_id (FK ke keluhan_armada)
- armada_id
- jenis ('spk', 'pembelian', 'service', dll)
- keterangan
- tanggal
- status (open, in_progress, closed)
```

## Flow Lengkap

### 1. Buat Keluhan Armada
```
User → Create Keluhan → Auto Generate Kode KEL/SDA/2602/001
Status Approval: pending
Status: waiting
```

### 2. Approval Keluhan
User klik "Approve" pada keluhan → Muncul modal dengan pilihan:

#### Pilihan A: Buat SPK Baru
```
Input:
- Kategori Keluhan: Darurat/Sedang/Minor

Proses:
1. Update keluhan:
   - kategori_keluhan = input
   - status_approval = 'approved'
   - status = 'in progress'
   - approved_by = user_id
   - approved_at = now()

2. Create SPK baru di keluhan_tindak_lanjut:
   - nomor_spk = Auto-generate SPK/SDA/2602/001
   - keluhan_id = keluhan.id
   - armada_id = keluhan.armada_id
   - jenis = 'spk'
   - status = 'open'
   - keterangan = 'SPK dibuat otomatis dari approval keluhan'

Result:
- Keluhan: KEL/SDA/2602/001 (approved, in progress)
- SPK: SPK/SDA/2602/001 (open) → Muncul di Menu SPK
```

#### Pilihan B: Gabung dengan SPK Open
```
Input:
- Kategori Keluhan: Darurat/Sedang/Minor
- Pilih SPK yang masih open

Proses:
1. Update keluhan:
   - kategori_keluhan = input
   - status_approval = 'approved'
   - status = 'in progress'

2. Keluhan terhubung dengan SPK existing
   (tidak create SPK baru)

Result:
- Keluhan: KEL/SDA/2602/002 (approved, in progress)
- SPK: SPK/SDA/2602/001 (tetap open, ada 2 keluhan)
```

#### Pilihan C: Langsung Close
```
Input:
- Kategori Keluhan: Darurat/Sedang/Minor
- Keterangan Rekomendasi
- Tanggal Deadline

Proses:
1. Update keluhan:
   - kategori_keluhan = input
   - status_approval = 'approved'
   - status = 'done'
   - keterangan_approval = input
   - tanggal_deadline = input

Result:
- Keluhan: KEL/SDA/2602/003 (approved, done)
- SPK: Tidak dibuat
```

## Fitur Menu SPK

### List SPK
- Menampilkan semua SPK (dari `keluhan_tindak_lanjut` where `jenis='spk'`)
- Filter berdasarkan:
  - Tanggal (dari - sampai)
  - Status (open, in_progress, closed)
- Kolom yang ditampilkan:
  - **Nomor SPK** (SPK/SDA/YYMM/XXX)
  - **Kode Keluhan** (KEL/SDA/YYMM/XXX)
  - Armada
  - Kategori Keluhan (Darurat/Sedang/Minor)
  - Tanggal
  - Jumlah Item
  - Status
  - Aksi

### Detail SPK
- Informasi lengkap SPK
- Detail keluhan yang terkait
- List item tindak lanjut
- History perubahan

### Edit SPK
- Update keterangan
- Update tanggal
- **Tidak bisa mengubah nomor SPK** (auto-generated, read-only)

### Close SPK
- Menutup SPK yang sudah selesai
- Otomatis update status keluhan menjadi 'done'
- SPK yang sudah closed tidak bisa dibuka lagi

## Auto-Generate Nomor

### Kode Keluhan (KEL/SDA/YYMM/XXX)
```php
// Di KeluhanArmada::genereteKodeKeluhan()
$prefix = 'KEL/SDA/' . date('ym') . '/';
// Ambil nomor terakhir di bulan yang sama
// Increment +1
return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
```

### Nomor SPK (SPK/SDA/YYMM/XXX)
```php
// Di KeluhanTindakLanjut::generateNomorSpk()
$prefix = 'SPK/SDA/' . date('ym') . '/';
// Ambil nomor terakhir di bulan yang sama WHERE jenis='spk'
// Increment +1
return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
```

**Penting**: Kedua nomor ini **reset setiap bulan** dan **independent** satu sama lain.

## Routes

```php
// List & Data
Route::get('spk', [SpkController::class, 'index'])->name('spk.index');
Route::get('spk/get-data', [SpkController::class, 'get_data'])->name('spk.get-data');

// Detail & Edit
Route::get('spk/{id}/detail', [SpkController::class, 'show'])->name('spk.detail');
Route::get('spk/{id}/edit', [SpkController::class, 'edit'])->name('spk.edit');
Route::put('spk/{id}', [SpkController::class, 'update'])->name('spk.update');

// Actions
Route::post('spk/{id}/close', [SpkController::class, 'close'])->name('spk.close');
Route::delete('spk/{id}', [SpkController::class, 'destroy'])->name('spk.destroy');

// Approval Keluhan (generate SPK)
Route::post('keluhan-armada/{id}/approve', [KeluhanArmadaController::class, 'approve'])
    ->name('keluhan-armada.approve');
Route::get('keluhan-armada/get-open-spk/{armadaId?}', [KeluhanArmadaController::class, 'getOpenSpk'])
    ->name('keluhan-armada.get-open-spk');
```

## Files

### Controllers
- `Modules/Operasional/Http/Controllers/SpkController.php`
- `Modules/Operasional/Http/Controllers/KeluhanArmadaController.php` (method `approve()`)

### Views
- `Modules/Operasional/Resources/views/spk/index.blade.php`
- `Modules/Operasional/Resources/views/spk/show.blade.php`
- `Modules/Operasional/Resources/views/spk/edit.blade.php`
- `Modules/Operasional/Resources/views/keluhan-armada/partials/modal-approval.blade.php`

### Entities
- `Modules/Operasional/Entities/KeluhanArmada.php`
  - Method `genereteKodeKeluhan()` untuk auto-generate kode keluhan
- `Modules/Operasional/Entities/KeluhanTindakLanjut.php`
  - Method `generateNomorSpk()` untuk auto-generate nomor SPK
  - Method `booted()` untuk trigger auto-generate saat create dengan jenis='spk'

## Migrations
- `2026_02_10_000001_add_approval_fields_to_keluhan_armada_table.php`
- `2026_02_10_000002_add_status_to_keluhan_tindak_lanjut_table.php`
- `2026_02_10_000003_add_nomor_spk_to_keluhan_tindak_lanjut_table.php`
- `2026_02_10_120000_update_keluhan_armada_kode_prefix.php` (update data lama)

## Testing

### Test 1: Create Keluhan Baru
```
1. Buat keluhan armada baru
2. Cek kode keluhan: KEL/SDA/2602/XXX ✓
3. Cek status_approval: pending ✓
4. Cek status: waiting ✓
```

### Test 2: Approval → Buat SPK Baru
```
1. Buka keluhan dengan status_approval = pending
2. Klik tombol "Approve"
3. Pilih kategori: Darurat
4. Pilih tindak lanjut: "Buat SPK Baru"
5. Submit

Expected Result:
- Keluhan status_approval = approved ✓
- Keluhan status = in progress ✓
- Keluhan kategori_keluhan = darurat ✓
- SPK baru muncul di menu SPK ✓
- SPK nomor: SPK/SDA/2602/XXX ✓
- SPK status: open ✓
```

### Test 3: Close SPK
```
1. Buka detail SPK yang status = open
2. Klik tombol "Close SPK"
3. Konfirmasi close

Expected Result:
- SPK status = closed ✓
- Keluhan status = done ✓
```

## Troubleshooting

### SPK tidak muncul setelah approval
**Penyebab**: 
- Approval dilakukan sebelum fix prefix KEL/SDA/
- Error saat create SPK tidak terdeteksi

**Solusi**:
1. Cek log error di `storage/logs/laravel.log`
2. Cek database: `SELECT * FROM keluhan_tindak_lanjut WHERE jenis='spk'`
3. Pastikan tidak ada duplicate key error
4. Re-approve keluhan jika perlu

### Duplicate entry error
**Penyebab**: 
- Keluhan armada masih pakai prefix SPK/SDA/ (seharusnya KEL/SDA/)

**Solusi**:
1. Jalankan migration: `php artisan migrate`
2. Migration akan update semua kode keluhan dari SPK/SDA/ ke KEL/SDA/

## Notes
- ✅ Keluhan dan SPK adalah entitas terpisah dengan kode berbeda
- ✅ Kode Keluhan: KEL/SDA/YYMM/XXX
- ✅ Nomor SPK: SPK/SDA/YYMM/XXX
- ✅ SPK hanya dibuat melalui approval keluhan (tidak bisa manual)
- ✅ Nomor SPK auto-generated dan tidak bisa diubah
- ✅ SPK yang sudah closed tidak bisa dibuka lagi
- ✅ Satu keluhan bisa menghasilkan satu SPK atau bergabung dengan SPK existing
- ✅ Nomor reset setiap bulan untuk masing-masing entitas
