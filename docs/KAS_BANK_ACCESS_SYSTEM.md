# Sistem Akses Kas/Bank Berdasarkan Role

## Overview
Sistem ini memungkinkan administrator untuk mengatur akses kas/bank yang spesifik untuk setiap role. User hanya dapat melihat dan menggunakan kas/bank yang telah diizinkan untuk role mereka.

## Komponen Sistem

### 1. Database
- **Tabel**: `role_kas_bank`
- **Struktur**:
  - `id`: Primary key
  - `id_role`: Foreign key ke tabel `role`
  - `kas_bank_id`: Foreign key ke tabel `m_ac_4` (id_ac)
  - `created_at`, `updated_at`: Timestamps

### 2. Model
- **RoleKasBank**: Model utama untuk mengelola relasi role-kasbank
- **Role**: Ditambahkan relationship `roleKasBank()`
- **User**: Ditambahkan method `getAccessibleKasBank()` dan `canAccessKasBank()`

### 3. Trait
- **HasKasBankAccess**: Trait untuk controller yang membutuhkan filter kas/bank

### 4. Helper
- **KasBankHelper**: Helper class untuk fungsi-fungsi umum terkait kas/bank

## Cara Penggunaan

### A. Mengatur Akses Kas/Bank untuk Role (Admin)

1. Masuk ke menu **Role Management**
2. Pilih role yang ingin diatur
3. Klik tab **"Akses Kas/Bank"**
4. Pilih kas/bank yang dapat diakses role tersebut
5. Klik **"Simpan Akses Kas/Bank"**

### B. Menggunakan di Controller

```php
<?php
namespace Modules\Keuangan\Http\Controllers;

use App\Traits\HasKasBankAccess;
use Illuminate\Routing\Controller;

class ExampleController extends Controller
{
    use HasKasBankAccess;

    public function create()
    {
        // Mendapatkan kas/bank yang dapat diakses user
        $data['kas_bank'] = $this->getKasBankOptions();
        
        // Cek apakah user memiliki akses
        if ($data['kas_bank']->isEmpty()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke Kas/Bank manapun.');
        }
        
        return view('example.create', $data);
    }

    public function store(Request $request)
    {
        // Validasi akses kas/bank sebelum menyimpan
        $this->authorizeKasBankAccess($request->kas_bank_id);
        
        // Lanjutkan proses penyimpanan...
    }

    public function get_data(Request $request)
    {
        $query = ExampleModel::query();
        
        // Filter berdasarkan kas/bank yang dapat diakses user
        $query = $this->filterByUserKasBankAccess($query, 'kas_bank_id');
        
        return DataTables::of($query)->make(true);
    }
}
```

### C. Menggunakan di Blade Template

```blade
{{-- Dropdown kas/bank yang sudah difilter --}}
<select name="kas_bank_id" class="form-control">
    <option value="">-- Pilih Kas/Bank --</option>
    @foreach($kas_bank as $kb)
        <option value="{{ $kb->id_ac }}">{{ $kb->id_ac }} - {{ $kb->nama }}</option>
    @endforeach
</select>

{{-- Menggunakan helper --}}
@if(App\Helpers\KasBankHelper::canAccessKasBank($kasBank->id_ac))
    <span>{{ App\Helpers\KasBankHelper::getKasBankName($kasBank->id_ac) }}</span>
@else
    <span class="text-muted">Akses Terbatas</span>
@endif
```

### D. Menggunakan Static Methods

```php
// Mendapatkan kas/bank untuk user saat ini
$kasBankList = RoleKasBank::getKasBankForCurrentUser();

// Mendapatkan array ID kas/bank
$kasBankIds = RoleKasBank::getKasBankIdsForCurrentUser();

// Cek akses ke kas/bank tertentu
$canAccess = RoleKasBank::canAccessKasBank($kasBankId);

// Mendapatkan query builder yang sudah difilter
$query = RoleKasBank::getKasBankQueryForCurrentUser();
```

## Method yang Tersedia

### RoleKasBank Model

| Method | Description | Return Type |
|--------|-------------|-------------|
| `getKasBankForCurrentUser()` | Mendapatkan collection kas/bank untuk user saat ini | Collection |
| `getKasBankIdsForCurrentUser()` | Mendapatkan array ID kas/bank untuk user saat ini | Array |
| `canAccessKasBank($id)` | Cek apakah user dapat mengakses kas/bank tertentu | Boolean |
| `getKasBankQueryForCurrentUser()` | Mendapatkan query builder yang sudah difilter | Builder |

### HasKasBankAccess Trait

| Method | Description | Parameters |
|--------|-------------|------------|
| `filterByUserKasBankAccess($query, $column)` | Filter query berdasarkan akses user | Query, Column name |
| `getKasBankOptions()` | Mendapatkan kas/bank options untuk dropdown | - |
| `validateKasBankAccess($id)` | Validasi akses kas/bank | Kas/Bank ID |
| `authorizeKasBankAccess($id)` | Throw exception jika tidak ada akses | Kas/Bank ID |

### KasBankHelper Class

| Method | Description | Parameters |
|--------|-------------|------------|
| `getKasBankOptions()` | Mendapatkan kas/bank options | - |
| `canAccessKasBank($id)` | Cek akses kas/bank | Kas/Bank ID |
| `getKasBankName($id)` | Mendapatkan nama kas/bank jika ada akses | Kas/Bank ID |
| `filterAccessibleKasBank($ids)` | Filter array ID berdasarkan akses | Array of IDs |

## Keamanan

1. **Validasi di Controller**: Setiap aksi yang melibatkan kas/bank harus divalidasi
2. **Filter di Query**: Data yang ditampilkan otomatis difilter berdasarkan akses
3. **Authorization**: Exception akan dilempar jika user mencoba mengakses kas/bank yang tidak diizinkan
4. **UI Protection**: Dropdown dan pilihan hanya menampilkan kas/bank yang dapat diakses

## Contoh Implementasi Lengkap

Lihat file `Modules/Keuangan/Http/Controllers/KasBankMasukController.php` untuk contoh implementasi lengkap yang mencakup:
- Filter di index/listing
- Validasi di create/store
- Validasi di edit/update
- Validasi di show/detail
- Filter dropdown di form

## Troubleshooting

### User tidak bisa melihat kas/bank apapun
- Pastikan user memiliki role yang sudah diatur akses kas/banknya
- Cek di menu Role Management → pilih role → tab "Akses Kas/Bank"

### Error "Anda tidak memiliki akses ke Kas/Bank ini"
- User mencoba mengakses kas/bank yang tidak diizinkan untuk rolenya
- Administrator perlu menambahkan akses kas/bank untuk role tersebut

### Dropdown kas/bank kosong
- Role user belum diatur akses kas/banknya
- Tidak ada data kas/bank di tabel `m_ac_4` dengan `head=1` dan `level=1`