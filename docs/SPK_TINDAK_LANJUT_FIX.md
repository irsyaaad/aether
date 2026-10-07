# SPK Tindak Lanjut Data Persistence Fix

## Problem
Data yang ditambahkan di halaman SPK detail (tindak lanjut items) hilang setelah refresh halaman, meskipun data berhasil disimpan via AJAX.

## Root Cause
Ketika items ditambahkan via AJAX di context SPK:
1. Items disimpan ke `keluhan_tindak_lanjut_items` table
2. Record `KeluhanTindakLanjut` dibuat/diupdate dengan `jenis` = pembelian/service/pemakaian
3. **MASALAH**: Field `nomor_spk` tidak di-set pada record `KeluhanTindakLanjut`
4. Query di `show.blade.php` filter by `nomor_spk` → data tidak muncul karena `nomor_spk` NULL

## Solution

### 1. Update Controller - KeluhanTindakLanjutController.php
**File**: `Modules/Operasional/Http/Controllers/KeluhanTindakLanjutController.php`

**Method**: `storeItemAjax()`

**Changes**:
- Accept `nomor_spk` parameter from request
- When creating new `KeluhanTindakLanjut` record, include `nomor_spk` in the data
- When finding existing record, also match by `nomor_spk` if provided

```php
// Check if tindak-lanjut already exists for this jenis and keluhan
// If nomor_spk is provided (SPK context), also match by nomor_spk
$query = KeluhanTindakLanjut::where('keluhan_id', $request->keluhan_id)
    ->where('jenis', $request->jenis);

if ($request->nomor_spk) {
    $query->where('nomor_spk', $request->nomor_spk);
}

$tindakLanjut = $query->first();

if (! $tindakLanjut) {
    $createData = [
        'keluhan_id' => $keluhan->id,
        'armada_id'  => $keluhan->armada_id,
        'jenis'      => $request->jenis,
        'keterangan' => 'Auto-created for ' . $request->jenis,
        'tanggal'    => now(),
    ];
    
    // Add nomor_spk if provided (SPK context)
    if ($request->nomor_spk) {
        $createData['nomor_spk'] = $request->nomor_spk;
    }
    
    $tindakLanjut = KeluhanTindakLanjut::create($createData);
}
```

### 2. Update JavaScript - javascript-inline-editing.blade.php
**File**: `Modules/Operasional/Resources/views/spk/segment/javascript-inline-editing.blade.php`

**Changes**:
- Pass `nomor_spk` in FormData when adding items

```javascript
let formData = new FormData();
formData.append('_token', "{{ csrf_token() }}");
formData.append('keluhan_id', keluhanId);
formData.append('jenis', type);
formData.append('nomor_spk', "{{ $spk->nomor_spk }}"); // Pass SPK nomor
// ... rest of the data
```

## Data Flow

### Before Fix:
```
User adds item → AJAX call → storeItemAjax()
→ Create KeluhanTindakLanjut (nomor_spk = NULL)
→ Create KeluhanTindakLanjutItem
→ Page refresh → Query by nomor_spk → No results (nomor_spk is NULL)
```

### After Fix:
```
User adds item → AJAX call with nomor_spk → storeItemAjax()
→ Create KeluhanTindakLanjut (nomor_spk = "SPK/SDA/2602/001")
→ Create KeluhanTindakLanjutItem
→ Page refresh → Query by nomor_spk → Results found!
```

## Database Structure

### keluhan_tindak_lanjut table
- `id` (PK)
- `nomor_spk` (nullable, string) - Links multiple keluhan to one SPK
- `keluhan_id` (FK to keluhan_armada)
- `armada_id` (FK to armada)
- `jenis` (enum: 'pembelian', 'service', 'pemakaian', 'spk')
- `keterangan`
- `tanggal`
- `status`

### keluhan_tindak_lanjut_items table
- `id` (PK)
- `keluhan_tindak_lanjut_id` (FK to keluhan_tindak_lanjut)
- `barang_id` (FK to barang)
- `armada_id` (FK to armada)
- `nama_barang`
- `qty`
- `harga`
- `total`
- `foto`
- `purchase_id`, `service_id`, `pemakaian_id` (nullable FKs)

## Testing

### Test Case 1: Add Pembelian Item
1. Buka SPK detail page
2. Pilih keluhan dari dropdown
3. Tambah item pembelian
4. Klik simpan
5. Refresh page
6. **Expected**: Item masih muncul di table

### Test Case 2: Add Service Item
1. Buka SPK detail page
2. Pilih keluhan dari dropdown
3. Tambah item service
4. Klik simpan
5. Refresh page
6. **Expected**: Item masih muncul di table

### Test Case 3: Add Pemakaian Item
1. Buka SPK detail page
2. Pilih keluhan dari dropdown
3. Tambah item pemakaian
4. Klik simpan
5. Refresh page
6. **Expected**: Item masih muncul di table

### Test Case 4: Multiple Keluhan in One SPK
1. Buka SPK detail page dengan multiple keluhan
2. Pilih keluhan A, tambah item
3. Pilih keluhan B, tambah item
4. Refresh page
5. **Expected**: Semua items dari kedua keluhan muncul dengan "Kode Keluhan" yang sesuai

## Verification Query

Check database after adding item:

```sql
-- Check if nomor_spk is set
SELECT id, nomor_spk, keluhan_id, jenis, created_at 
FROM keluhan_tindak_lanjut 
WHERE nomor_spk = 'SPK/SDA/2602/001'
AND jenis != 'spk';

-- Check items
SELECT ktli.*, ktl.nomor_spk, ktl.jenis
FROM keluhan_tindak_lanjut_items ktli
JOIN keluhan_tindak_lanjut ktl ON ktli.keluhan_tindak_lanjut_id = ktl.id
WHERE ktl.nomor_spk = 'SPK/SDA/2602/001';
```

## Related Files
- `Modules/Operasional/Http/Controllers/KeluhanTindakLanjutController.php`
- `Modules/Operasional/Resources/views/spk/segment/javascript-inline-editing.blade.php`
- `Modules/Operasional/Resources/views/spk/show.blade.php`
- `Modules/Operasional/Entities/KeluhanTindakLanjut.php`
- `Modules/Operasional/Entities/KeluhanTindakLanjutItem.php`
