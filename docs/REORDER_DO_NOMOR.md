# Dokumentasi: Mengurutkan Ulang Nomor DO

## Deskripsi
Function untuk mengurutkan ulang nomor DO berdasarkan bulan tertentu. Nomor DO akan diurutkan berdasarkan `tanggal_do` (tanggal delivery order) secara ascending.

Format nomor DO: `DO/SDA/YYMM/XXX`
- YY = 2 digit tahun terakhir (contoh: 26 untuk 2026)
- MM = 2 digit bulan (01-12)
- XXX = nomor urut 3 digit (001, 002, dst)

## Cara Penggunaan

### 1. Via Artisan Command (Recommended)

```bash
# Mengurutkan DO bulan Februari 2026 (default)
php artisan do:reorder-februari

# Mengurutkan DO bulan Februari tahun tertentu
php artisan do:reorder-februari 2025
```

**Output:**
```
Memulai pengurutan ulang nomor DO untuk Februari 2026...
Ditemukan 45 DO di bulan Februari 2026
  [1] DO/SDA/2602/005 → DO/SDA/2602/001 (Tanggal DO: 2026-02-01)
  [2] DO/SDA/2602/003 → DO/SDA/2602/002 (Tanggal DO: 2026-02-01)
  [3] DO/SDA/2602/007 → DO/SDA/2602/003 (Tanggal DO: 2026-02-02)
  ...
✓ Berhasil mengurutkan ulang 45 nomor DO di bulan Februari 2026
```

### 2. Via API/Controller

**Endpoint:** `POST /do/reorder-month`

**Request Body:**
```json
{
  "year": 2026,
  "month": 2
}
```

**Response Success:**
```json
{
  "status": "success",
  "message": "Berhasil mengurutkan ulang 45 nomor DO di bulan 2/2026",
  "updated": 45
}
```

**Response Error:**
```json
{
  "status": "error",
  "message": "Tidak ada data DO di bulan 2/2026"
}
```

**Contoh menggunakan cURL:**
```bash
curl -X POST http://localhost:8000/do/reorder-month \
  -H "Content-Type: application/json" \
  -d '{"year": 2026, "month": 2}'
```

**Contoh menggunakan JavaScript/AJAX:**
```javascript
$.ajax({
    url: '/do/reorder-month',
    type: 'POST',
    data: {
        year: 2026,
        month: 2,
        _token: $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
        alert(response.message);
        console.log('Updated:', response.updated);
    },
    error: function(xhr) {
        alert('Error: ' + xhr.responseJSON.message);
    }
});
```

### 3. Via Model Method (Programmatic)

```php
use Modules\Operasional\Entities\DoModel;

// Mengurutkan DO bulan Februari 2026
$result = DoModel::reorderDoByMonth(2026, 2);

if ($result['success']) {
    echo $result['message'];
    echo "Total diupdate: " . $result['updated'];
} else {
    echo "Error: " . $result['message'];
}
```

## Catatan Penting

1. **Backup Data**: Disarankan untuk backup database sebelum menjalankan reorder
2. **Soft Delete**: Function ini juga memproses data yang sudah di-soft delete
3. **Urutan**: Data diurutkan berdasarkan `tanggal_do` ASC, kemudian `id` ASC
4. **Transaction**: Semua perubahan dibungkus dalam database transaction
5. **No Trigger**: Update dilakukan langsung ke database tanpa trigger model events
6. **Filter**: Hanya DO dengan `tanggal_do` di bulan yang dipilih yang akan diproses

## Kapan Menggunakan

- Ketika ada nomor DO yang loncat/tidak berurutan
- Setelah menghapus beberapa DO dan ingin merapikan nomor
- Ketika ada kesalahan input tanggal yang menyebabkan nomor tidak urut
- Di akhir bulan untuk memastikan nomor rapi dan berurutan

## Troubleshooting

**Q: Nomor masih tidak urut setelah reorder**
A: Pastikan filter bulan dan tahun sudah benar. Cek juga field `tanggal_do` pada data DO.

**Q: Error "Tidak ada data DO"**
A: Pastikan ada data DO di bulan dan tahun yang dipilih. Cek dengan query:
```sql
SELECT COUNT(*) FROM do 
WHERE YEAR(tanggal_do) = 2026 
AND MONTH(tanggal_do) = 2;
```

**Q: Apakah aman untuk production?**
A: Ya, karena menggunakan transaction. Jika terjadi error, semua perubahan akan di-rollback.

**Q: Apakah data yang sudah dihapus (soft delete) ikut diproses?**
A: Ya, untuk memastikan nomor tetap konsisten dan tidak ada gap dalam penomoran.

