# Fix: Batal Bayar Biaya Feature - Bug Fix

## Issue
The `batalBayar` method in DoController had two critical bugs:
1. Wrong field name when restoring piutang (kasbon)
2. Trying to update non-existent `tanggal_bayar` column in `do_biaya` table

## Root Causes
1. The code was trying to access `$piutangBayar->piutang_id` but the correct field name in the `piutang_karyawan_pembayaran` table is `piutang_karyawan_id`
2. The `do_biaya` table doesn't have a `tanggal_bayar` column (only `do_biaya_bayar` has it)

## Changes Made

### File: `Modules/Operasional/Http/Controllers/DoController.php`

#### Bug 1: Wrong piutang field name
**Before:**
```php
$piutang = PiutangKaryawan::find($piutangBayar->piutang_id);
```

**After:**
```php
$piutang = PiutangKaryawan::find($piutangBayar->piutang_karyawan_id);
```

#### Bug 2: Non-existent tanggal_bayar column
**Before:**
```php
$biaya->status = 'belum dibayar';
$biaya->tanggal_bayar = null;  // ❌ Column doesn't exist
$biaya->info_bayar = null;
$biaya->save();
```

**After:**
```php
$biaya->status = 'belum dibayar';
$biaya->info_bayar = null;
$biaya->save();
```

## Complete Fixed Method

```php
public function batalBayar(Request $request)
{
    if ($denied = $this->requireAccess('do.update')) {
        return $denied;
    }

    $request->validate([
        'id_do'    => 'required|exists:do,id',
        'id_biaya' => 'required|exists:do_biaya,id',
    ]);

    try {
        DB::beginTransaction();

        $biaya = DoBiaya::findOrFail($request->id_biaya);
        $oldBiaya = $biaya->toArray();

        // Get all pembayaran for this biaya
        $pembayaranList = DoBiayaBayar::where('biaya_id', $request->id_biaya)->get();

        foreach ($pembayaranList as $pembayaran) {
            $oldPembayaran = $pembayaran->toArray();

            // If there's kasbon payment, restore piutang
            if ($pembayaran->piutang_bayar_id) {
                $piutangBayar = PiutangKaryawanPembayaran::find($pembayaran->piutang_bayar_id);
                if ($piutangBayar) {
                    $piutang = PiutangKaryawan::find($piutangBayar->piutang_karyawan_id);
                    if ($piutang) {
                        // Restore sisa piutang and jumlah_terbayar
                        $piutang->sisa_piutang += $piutangBayar->jumlah_bayar;
                        $piutang->jumlah_terbayar -= $piutangBayar->jumlah_bayar;
                        
                        // Update status if needed
                        if ($piutang->sisa_piutang > 0) {
                            $piutang->status = 'aktif';
                        }
                        $piutang->save();

                        // Log audit trails
                        AuditService::log(...);
                        
                        // Delete piutang pembayaran
                        $piutangBayar->delete();
                    }
                }
            }

            // Log and delete pembayaran
            AuditService::log('delete', 'do_biaya_bayar', $pembayaran->id, $oldPembayaran, null);
            $pembayaran->delete();
        }

        // Update biaya status back to belum dibayar
        $biaya->status = 'belum dibayar';
        $biaya->info_bayar = null;
        $biaya->save();

        // Log audit trail for biaya update
        AuditService::log('update', 'do_biaya', $biaya->id, $oldBiaya, $biaya->toArray());

        DB::commit();

        return redirect()->back()->with('success', 'Pembayaran biaya berhasil dibatalkan');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal membatalkan pembayaran: ' . $e->getMessage());
    }
}
```

## Database Schema Reference

### do_biaya table columns:
- id
- do_id
- nama_biaya
- ac_debet
- ac_kredit
- biaya
- keterangan
- status (enum: 'belum dibayar', 'lunas')
- info_bayar
- type (enum: 'uj', 'non_uj', 'kasbon')
- created_by
- updated_by
- timestamps
- softDeletes

### do_biaya_bayar table columns:
- id
- do_id
- biaya_id
- tanggal_bayar ✅ (exists here, not in do_biaya)
- nama_biaya
- ac_debet
- ac_kredit
- biaya
- bukti_bayar
- keterangan
- piutang_bayar_id
- created_by
- updated_by
- timestamps
- softDeletes

## Testing
To test this feature:
1. Create a DO with biaya
2. Pay the biaya with kasbon (potong kasbon)
3. Verify piutang is reduced
4. Click "Batal Bayar" button
5. Verify:
   - Payment is deleted from do_biaya_bayar
   - Biaya status returns to "belum dibayar"
   - Piutang is restored correctly (sisa_piutang increased, jumlah_terbayar decreased)
   - Audit logs are created for all changes
   - No SQL errors occur

## Related Files
- `Modules/Operasional/Http/Controllers/DoController.php` - batalBayar method
- `Modules/Operasional/Resources/views/do/segment/biaya.blade.php` - Modal and JavaScript
- `Modules/Operasional/Resources/views/do/segment/partials/biaya-uj.blade.php` - Batal button
- `Modules/Operasional/Routes/web.php` - Route definition
- `database/migrations/2025_12_02_131349_create_do_table.php` - Table structure

