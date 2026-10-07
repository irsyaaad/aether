# Menu UJ DO

## Deskripsi
Menu UJ DO adalah halaman untuk memonitor transaksi uang jalan (UJ) Delivery Order yang mencakup:
1. Transaksi Kas/Bank Masuk dari BCA KBBSASTROB (AC: 1021)
2. Pelunasan biaya DO
3. List biaya DO yang statusnya ready pada hari tertentu

## Lokasi File

### Controller
- `Modules/Keuangan/Http/Controllers/UjDoController.php`

### View
- `Modules/Keuangan/Resources/views/uj-do/index.blade.php`

### Routes
- `Modules/Keuangan/Routes/web.php`
  - GET `uj-do` → index
  - GET `uj-do/get-data` → getData (DataTables)

## Fitur

### 1. Filter Tanggal
- User dapat memilih tanggal untuk melihat transaksi pada hari tertentu
- Default: tanggal hari ini

### 2. Summary Cards
Menampilkan 3 card summary:
- **Total Kas/Bank Masuk**: Total transaksi kas/bank masuk dari BCA KBBSASTROB
- **Total Pelunasan DO**: Total pelunasan biaya DO pada hari tersebut
- **Total Biaya Ready**: Total biaya DO yang di-set ready pada hari tersebut

### 3. DataTable
Menampilkan list transaksi dengan kolom:
- No
- Tanggal
- Tipe (badge: Kas/Bank Masuk, Pelunasan DO, Biaya Ready)
- Kode (kode transaksi atau kode DO)
- Keterangan
- Nominal
- User (yang membuat transaksi)
- Aksi (link ke detail)

## Logika Data

### Kas/Bank Masuk
```php
// Ambil transaksi kas/bank masuk yang:
// - Tanggal sesuai filter
// - Detail memiliki ac_kredit = 1021 (BCA KBBSASTROB)
KasBankMasuk::where('tanggal', $tanggal)
    ->whereHas('details', function ($q) {
        $q->where('ac_kredit', 1021);
    })
```

### Pelunasan DO
```php
// Ambil pelunasan biaya DO yang:
// - Tanggal bayar sesuai filter
DoBiayaBayar::whereDate('tanggal_bayar', $tanggal)
```

### Biaya Ready
```php
// Ambil biaya DO yang:
// - Status ready = 'ready'
// - Tanggal ready sesuai filter
DoBiaya::where('status_ready', 'ready')
    ->whereDate('tanggal_ready', $tanggal)
```

## Access Control
- Permission: `uj-do.index`
- Menu terletak di: Keuangan → DO Keuangan → UJ DO

## Integrasi dengan Fitur Lain

### Set Ready Biaya DO
- Biaya DO yang di-set ready akan muncul di menu UJ DO pada tanggal ready-nya
- Button "Set Ready" ada di kolom "Detail UJ" pada menu DO (index)
- Tanggal ready default: besok (H+1)
- Button tidak muncul jika biaya sudah lunas

### Link ke Detail
- Kas/Bank Masuk → link ke detail kas/bank masuk
- Pelunasan DO → link ke detail DO (do-keuangan)
- Biaya Ready → link ke detail DO (do-keuangan)

## Catatan Teknis
- Menggunakan DataTables server-side processing
- Data digabung dari 3 sumber berbeda menggunakan Collection
- Sort by created_at descending
- Format tanggal menggunakan helper `dateindo()`
- Format nominal menggunakan `number_format()` dengan format Indonesia
