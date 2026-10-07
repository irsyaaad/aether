# Sistem Arsip Dokumentasi Perusahaan

## Deskripsi
Sistem untuk mengelola arsip foto dan video dokumentasi perusahaan dengan fitur upload, kategorisasi, dan pencarian.

## Fitur Utama

### 📁 **Upload & Manajemen File**
- Upload foto (JPG, JPEG, PNG, GIF, WEBP) maksimal 10MB
- Upload video (MP4, AVI, MOV, WMV, FLV, WEBM) maksimal 50MB
- Preview file sebelum upload
- Auto-generate judul dari nama file
- Validasi format dan ukuran file

### 🏷️ **Kategorisasi & Metadata**
- **Kategori**: Kegiatan, Fasilitas, Karyawan, Produk, Acara, Lainnya
- **Status**: Aktif, Arsip, Dihapus
- **Tags**: Sistem tagging untuk pencarian mudah
- **Informasi**: Judul, deskripsi, tanggal dokumentasi, lokasi
- **Visibilitas**: Publik atau internal

### 🔍 **Pencarian & Filter**
- Filter berdasarkan tipe file (foto/video)
- Filter berdasarkan kategori
- Filter berdasarkan status
- Filter berdasarkan rentang tanggal
- Pencarian teks pada judul dan deskripsi
- DataTables dengan server-side processing

### 👁️ **Tampilan & Preview**
- Preview thumbnail untuk foto
- Video player untuk file video
- Detail view dengan informasi lengkap
- Download file asli
- Responsive design

### 🔐 **Keamanan & Akses**
- Sistem permission berbasis role
- Tracking uploader dan editor
- Soft delete untuk keamanan data
- Validasi file type dan size

## Struktur Database

### Tabel: `arsip_dokumentasi`
```sql
- id (Primary Key)
- judul (VARCHAR 255)
- deskripsi (TEXT)
- tipe_file (ENUM: foto, video)
- nama_file (VARCHAR)
- path_file (VARCHAR)
- ukuran_file (VARCHAR)
- format_file (VARCHAR)
- kategori (ENUM: kegiatan, fasilitas, karyawan, produk, acara, lainnya)
- tanggal_dokumentasi (DATE)
- lokasi (VARCHAR)
- tags (JSON)
- status (ENUM: aktif, arsip, dihapus)
- is_public (BOOLEAN)
- uploaded_by (Foreign Key)
- updated_by (Foreign Key)
- timestamps
- soft_deletes
```

## File Structure
```
app/
├── Models/ArsipDokumentasi.php
├── Http/Controllers/ArsipDokumentasiController.php
resources/views/arsip-dokumentasi/
├── index.blade.php
├── create.blade.php
├── show.blade.php
├── edit.blade.php
storage/app/public/arsip/
├── foto/
├── video/
```

## Routes
```php
GET    /arsip-dokumentasi              # Index (list)
GET    /arsip-dokumentasi/create       # Form upload
POST   /arsip-dokumentasi              # Store upload
GET    /arsip-dokumentasi/{id}/detail  # Show detail
GET    /arsip-dokumentasi/{id}/edit    # Form edit
PUT    /arsip-dokumentasi/{id}         # Update
DELETE /arsip-dokumentasi/{id}         # Delete
GET    /arsip-dokumentasi/{id}/download # Download file
GET    /arsip-dokumentasi/get-data     # DataTables AJAX
```

## Permissions Required
- `arsip-dokumentasi.index` - Melihat daftar
- `arsip-dokumentasi.create` - Upload baru
- `arsip-dokumentasi.update` - Edit data
- `arsip-dokumentasi.delete` - Hapus data

## Usage

### Upload Dokumentasi
1. Akses menu "Arsip Dokumentasi"
2. Klik "Upload Dokumentasi"
3. Pilih tipe file (foto/video)
4. Upload file dan isi informasi
5. Simpan

### Manajemen Arsip
1. Filter dan cari dokumentasi
2. Klik "Lihat" untuk detail
3. Edit informasi jika diperlukan
4. Download file asli
5. Ubah status sesuai kebutuhan

## Technical Notes

### File Storage
- Files disimpan di `storage/app/public/arsip/`
- Symbolic link ke `public/storage/`
- Nama file di-generate unique dengan timestamp

### Performance
- Server-side DataTables untuk handling data besar
- Eager loading untuk optimasi query
- File size validation untuk mencegah upload berlebihan

### Security
- File type validation
- Size limit enforcement
- Access control per action
- Soft delete untuk audit trail

## Installation
1. Run migration: `php artisan migrate`
2. Create storage link: `php artisan storage:link`
3. Set permissions sesuai role
4. Access via `/arsip-dokumentasi`