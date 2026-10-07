# SPK Quality Control (QC) Feature

## Overview
Fitur Quality Control (QC) untuk SPK memungkinkan pengecekan dan test drive armada setelah dilakukan pembelian, service, atau pemakaian spare part.

## Features

### 1. Tab Quality Control
- Tab khusus untuk menampilkan semua data QC
- Terintegrasi dengan tab lain (Informasi, Tindak Lanjut, Riwayat Status)
- Menampilkan history lengkap semua pengecekan QC

### 2. Form Tambah QC
- **Jenis Pengecekan**: Pembelian, Service, atau Pemakaian
- **Item**: Pilihan item spesifik yang diperiksa (opsional)
- **Hasil Pengecekan**: Deskripsi hasil pengecekan
- **Status QC**: 
  - Lulus (hijau)
  - Tidak Lulus (merah)
  - Perlu Perbaikan (kuning)
- **Test Drive**: Ya/Tidak
- **Hasil Test Drive**: Muncul jika test drive = Ya
- **Catatan**: Catatan tambahan

### 3. Tabel QC Records
Menampilkan:
- Nomor urut
- Jenis pengecekan (badge berwarna)
- Item yang diperiksa
- Hasil pengecekan
- Status QC (badge berwarna)
- Test drive (Ya/Tidak)
- Hasil test drive
- Catatan
- Diperiksa oleh (user)
- Waktu pengecekan
- Aksi (Edit/Hapus)

### 4. Edit & Delete QC
- Edit data QC yang sudah ada
- Hapus data QC dengan konfirmasi SweetAlert
- Hanya bisa dilakukan jika SPK belum closed

## Database Structure

### Table: `spk_qc`
```sql
- id (bigint, primary key)
- spk_id (bigint, foreign key to keluhan_tindak_lanjut.id)
- jenis_pengecekan (string: pembelian, service, pemakaian)
- item_id (bigint, nullable, foreign key to keluhan_tindak_lanjut_item.id)
- hasil_pengecekan (text, nullable)
- status_qc (enum: lulus, tidak_lulus, perlu_perbaikan)
- catatan (text, nullable)
- test_drive (enum: ya, tidak)
- hasil_test_drive (text, nullable)
- foto_qc (json, nullable) - Array foto hasil QC
- checked_by (int, user id)
- checked_at (timestamp)
- created_at (timestamp)
- updated_at (timestamp)
```

## Implementation Files

### 1. Migration
- `database/migrations/2026_02_18_000001_create_spk_qc_table.php`
  - Creates spk_qc table with all required fields
  - Foreign key to keluhan_tindak_lanjut (cascade delete)

### 2. Model
- `Modules/Operasional/Entities/SpkQc.php`
  - Relationships: spk(), item(), checker()
  - Casts: foto_qc (array), checked_at (datetime)
  - Static method: getStatusQcLabel($status) - returns label and color

### 3. Controller Methods
- `Modules/Operasional/Http/Controllers/SpkController.php`
  - `show()` - Updated to load QC records
  - `storeQc()` - Create new QC record
  - `updateQc()` - Update existing QC record
  - `deleteQc()` - Delete QC record

### 4. Routes
- `Modules/Operasional/Routes/web.php`
  - POST: `spk/{id}/qc` → SpkController@storeQc
  - PUT: `spk/{id}/qc/{qcId}` → SpkController@updateQc
  - DELETE: `spk/{id}/qc/{qcId}` → SpkController@deleteQc

### 5. Views
- `Modules/Operasional/Resources/views/spk/show.blade.php`
  - Added QC tab in navigation
  - QC tab content with table and actions
  - JavaScript for QC operations

- `Modules/Operasional/Resources/views/spk/segment/modal-qc.blade.php`
  - Modal Add QC
  - Modal Edit QC
  - Dynamic form fields based on test drive selection

## Usage Flow

### Tambah QC
1. Buka detail SPK
2. Klik tab "Quality Control (QC)"
3. Klik button "Tambah QC"
4. Isi form:
   - Pilih jenis pengecekan
   - (Opsional) Pilih item spesifik
   - Isi hasil pengecekan
   - Pilih status QC
   - Pilih apakah dilakukan test drive
   - Jika test drive = Ya, isi hasil test drive
   - (Opsional) Tambahkan catatan
5. Klik "Simpan"
6. Data QC tersimpan dan page refresh

### Edit QC
1. Klik button edit (kuning) pada row QC
2. Modal edit muncul dengan data terisi
3. Update data yang diperlukan
4. Klik "Update"
5. Data QC terupdate dan page refresh

### Hapus QC
1. Klik button hapus (merah) pada row QC
2. Konfirmasi dengan SweetAlert
3. Jika konfirmasi, data QC terhapus
4. Page refresh

## Status QC Badge Colors
- **Lulus**: Success (green) - Item lolos pengecekan
- **Tidak Lulus**: Danger (red) - Item tidak lolos pengecekan
- **Perlu Perbaikan**: Warning (yellow) - Item perlu perbaikan

## Jenis Pengecekan Badge Colors
- **Pembelian**: Success (green)
- **Service**: Info (blue)
- **Pemakaian**: Warning (yellow)

## Business Rules

1. **QC hanya bisa ditambah/edit/hapus jika SPK belum closed**
   - Button "Tambah QC" hidden jika status = closed
   - Button Edit/Hapus hidden jika status = closed

2. **Test Drive Field**
   - Field "Hasil Test Drive" hanya muncul jika Test Drive = Ya
   - Menggunakan jQuery slideDown/slideUp untuk smooth transition

3. **Item Selection**
   - Item bersifat opsional
   - Jika tidak dipilih, QC dianggap pengecekan umum
   - Item list diambil dari semua tindak lanjut dengan nomor SPK yang sama

4. **Audit Trail**
   - Semua operasi QC (create, update, delete) dicatat di audit log
   - Mencatat user yang melakukan operasi
   - Mencatat timestamp operasi

5. **User Tracking**
   - Field `checked_by` menyimpan ID user yang melakukan QC
   - Field `checked_at` menyimpan timestamp saat QC dilakukan
   - Otomatis terisi saat create QC

## Integration Points

### With SPK Status
- QC biasanya dilakukan saat status SPK = "QC"
- Namun bisa dilakukan di status manapun selama SPK belum closed

### With Tindak Lanjut
- QC bisa dikaitkan dengan item spesifik dari tindak lanjut
- Mendukung pengecekan per-item atau pengecekan umum

### With Armada
- Test drive dilakukan untuk memastikan armada berfungsi dengan baik
- Hasil test drive dicatat untuk dokumentasi

## Future Enhancements (Optional)

1. **Upload Foto QC**
   - Field `foto_qc` sudah tersedia (JSON array)
   - Bisa ditambahkan fitur upload foto hasil QC
   - Menampilkan foto di tabel atau modal detail

2. **QC Checklist**
   - Template checklist untuk setiap jenis pengecekan
   - Checkbox untuk item-item yang harus diperiksa

3. **QC Report**
   - Export QC records ke PDF/Excel
   - Summary QC per periode
   - Statistik status QC (lulus/tidak lulus)

4. **QC Approval**
   - Approval workflow untuk QC
   - Supervisor harus approve hasil QC

5. **QC Reminder**
   - Notifikasi jika SPK sudah selesai tapi belum ada QC
   - Reminder untuk melakukan test drive

## Notes
- QC records cascade delete jika SPK dihapus
- Semua operasi menggunakan AJAX untuk UX yang lebih baik
- SweetAlert digunakan untuk konfirmasi dan notifikasi
- Form validation menggunakan HTML5 required attribute
