# SPK Status Workflow Implementation

## Overview
Implementasi sistem manajemen status SPK (Surat Perintah Kerja) dengan tracking history lengkap dan perhitungan durasi pengerjaan.

## Status Workflow

```
Draft → Approved → On Progress → Waiting Part → Resume Work (Rework) → QC → Closed
                                                      ↓
                                                   Rework
```

### Status Available:
1. **Draft** - Status awal SPK
2. **Approved** - SPK telah disetujui
3. **On Progress** - SPK sedang dikerjakan
4. **Waiting Part** - Menunggu spare part
5. **Resume Work (Rework)** - Melanjutkan pekerjaan setelah part tersedia
6. **QC** - Quality Control
7. **Closed** - SPK selesai
8. **Rework** - Pekerjaan perlu dikerjakan ulang

## Features

### 1. Status Management Card
- Menampilkan status saat ini dengan badge berwarna
- Dropdown untuk memilih status baru
- Input catatan (opsional) untuk perubahan status
- Button untuk update status dengan konfirmasi SweetAlert

### 2. Status History Tracking
- Mencatat semua perubahan status
- Menampilkan:
  - Status dari → Status ke
  - Catatan perubahan
  - User yang melakukan perubahan
  - Timestamp perubahan
  - Durasi antara status
- Menghitung total waktu pengerjaan dari status pertama ke terakhir

### 3. Duration Calculation
- Menghitung durasi antara setiap perubahan status
- Format: X hari Y jam Z menit
- Total durasi ditampilkan di footer tabel history

## Database Structure

### Table: `spk_status_history`
```sql
- id (bigint, primary key)
- spk_id (bigint, foreign key to keluhan_tindak_lanjut.id)
- status_from (enum: draft, approved, on_progress, waiting_part, resume_work, qc, closed, rework)
- status_to (enum: draft, approved, on_progress, waiting_part, resume_work, qc, closed, rework)
- notes (text, nullable)
- changed_by (int, user id)
- created_at (timestamp)
- updated_at (timestamp)
```

### Updated: `keluhan_tindak_lanjut` table
```sql
- status column updated to enum: draft, approved, on_progress, waiting_part, resume_work, qc, closed, rework
- Default: 'draft'
```

## Implementation Files

### 1. Migration
- `database/migrations/2026_02_12_110212_update_spk_status_and_create_history_table.php`
  - Updates status enum in keluhan_tindak_lanjut table
  - Creates spk_status_history table

### 2. Model
- `Modules/Operasional/Entities/SpkStatusHistory.php`
  - Relationships: spk(), user()
  - Static method: getStatusLabel($status) - returns label and color

### 3. Controller
- `Modules/Operasional/Http/Controllers/SpkController.php`
  - Method: updateStatus(Request $request, $id)
  - Validates status input
  - Updates SPK status
  - Records history with user and timestamp
  - Logs to audit trail

### 4. Routes
- `Modules/Operasional/Routes/web.php`
  - POST: `spk/{id}/update-status` → SpkController@updateStatus

### 5. View
- `Modules/Operasional/Resources/views/spk/show.blade.php`
  - Status Management Card (line ~90-130)
  - Status History Table (line ~280-380)
  - JavaScript for AJAX update (line ~570-600)

## Usage

### Update Status
1. Pilih status baru dari dropdown
2. (Opsional) Masukkan catatan perubahan
3. Klik button "Update Status"
4. Konfirmasi dengan SweetAlert
5. Status akan diupdate dan history tercatat
6. Page akan refresh otomatis

### View History
- History ditampilkan dalam tabel di bawah informasi SPK
- Menampilkan semua perubahan status secara kronologis
- Durasi antara status dihitung otomatis
- Total waktu pengerjaan ditampilkan di footer

## Status Badge Colors
- Draft: Secondary (gray)
- Approved: Success (green)
- On Progress: Primary (blue)
- Waiting Part: Warning (yellow)
- Resume Work: Info (cyan)
- QC: Dark (black)
- Closed: Success (green)
- Rework: Danger (red)

## Audit Trail
Setiap perubahan status dicatat dalam audit log dengan:
- Action: 'update_status'
- Table: 'keluhan_tindak_lanjut'
- Old data: status lama
- New data: status baru
- User: user yang melakukan perubahan

## Notes
- Status history tidak dapat dihapus (cascade delete jika SPK dihapus)
- Perubahan status memerlukan konfirmasi user
- Catatan perubahan bersifat opsional
- Durasi dihitung berdasarkan created_at timestamp
- Total durasi menghitung dari history pertama ke terakhir
