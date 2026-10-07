# GPS ID Integration - Troubleshooting Guide

## Current Status

✅ **Connection**: Server dapat mengakses GPS ID API (IP: 34.101.214.193)  
❌ **Authentication**: Token generation gagal dengan HTTP 401 Unauthorized

## Error yang Terjadi

```
HTTP 401 Unauthorized
```

Semua endpoint yang dicoba mengembalikan status 401:
- `/api/login`
- `/api/auth/login`
- `/login`
- `/token`
- `/api/token`

## Kemungkinan Penyebab

### 1. Credentials Tidak Valid
- Username atau password salah
- Akun belum aktif atau expired
- Perlu verifikasi akun terlebih dahulu

**Solusi:**
- Verifikasi credentials dengan login manual ke portal GPS ID
- Hubungi admin GPS ID untuk memastikan akun aktif
- Reset password jika perlu

### 2. Format Request Tidak Sesuai
GPS ID mungkin mengharapkan format request yang berbeda:
- Header khusus (API Key, Client ID, dll)
- Parameter tambahan
- Format data yang spesifik

**Solusi:**
- Minta dokumentasi API resmi dari GPS ID
- Cek contoh request yang benar
- Tanyakan apakah perlu API key tambahan

### 3. Endpoint yang Salah
Base URL yang digunakan: `https://portal.gps.id/backend/seen/public`

Mungkin endpoint yang benar berbeda.

**Solusi:**
- Konfirmasi base URL yang benar dengan GPS ID
- Cek apakah ada versi API (v1, v2, dll)
- Tanyakan endpoint authentication yang benar

### 4. IP Whitelist
Server mungkin perlu didaftarkan di whitelist GPS ID.

**Solusi:**
- Tanyakan apakah ada IP whitelist
- Daftarkan IP server ke GPS ID

## Langkah Troubleshooting

### 1. Test Manual dengan cURL

Coba test manual untuk melihat response detail:

```bash
curl -X POST https://portal.gps.id/backend/seen/public/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"Persadaaj","password":"PERSADABD17"}' \
  -v
```

Atau dengan form data:

```bash
curl -X POST https://portal.gps.id/backend/seen/public/api/login \
  -d "username=Persadaaj" \
  -d "password=PERSADABD17" \
  -v
```

### 2. Cek Response Body

Lihat response body untuk mendapatkan informasi error yang lebih detail:

```bash
php artisan tinker
>>> $response = \Illuminate\Support\Facades\Http::post('https://portal.gps.id/backend/seen/public/api/login', ['username' => 'Persadaaj', 'password' => 'PERSADABD17']);
>>> echo $response->body();
```

### 3. Test dengan Postman

1. Buka Postman
2. Create new request POST
3. URL: `https://portal.gps.id/backend/seen/public/api/login`
4. Body: 
   ```json
   {
     "username": "Persadaaj",
     "password": "PERSADABD17"
   }
   ```
5. Lihat response dan status code

### 4. Hubungi GPS ID Support

Informasi yang perlu ditanyakan:
- ✅ Base URL API yang benar
- ✅ Endpoint untuk authentication
- ✅ Format request (JSON/Form/Query)
- ✅ Parameter yang diperlukan
- ✅ Header yang diperlukan (API Key, dll)
- ✅ Response format
- ✅ Token expiration time
- ✅ Endpoint untuk get vehicles
- ✅ Endpoint untuk get location
- ✅ Apakah perlu IP whitelist

## Informasi untuk GPS ID Support

Ketika menghubungi support, berikan informasi ini:

```
Username: Persadaaj
Server IP: [IP server Anda]
Error: HTTP 401 Unauthorized saat mencoba authenticate
Base URL yang digunakan: https://portal.gps.id/backend/seen/public
Endpoints yang sudah dicoba:
- /api/login
- /api/auth/login
- /login
- /token
- /api/token

Request format yang sudah dicoba:
- JSON: {"username":"Persadaaj","password":"***"}
- Form Data: username=Persadaaj&password=***
```

## Solusi Sementara

Sambil menunggu dokumentasi yang benar, Anda bisa:

### 1. Gunakan Mock Data

Update `GpsIdService` untuk return mock data saat development:

```php
public function getToken()
{
    if (config('app.env') === 'local') {
        return 'mock_token_for_development';
    }
    // ... existing code
}
```

### 2. Manual Integration

Jika GPS ID menyediakan web interface:
1. Login manual ke portal GPS ID
2. Copy data kendaraan secara manual
3. Input ke sistem armada

### 3. Alternative GPS Provider

Pertimbangkan GPS provider lain yang memiliki dokumentasi API yang jelas:
- Google Maps Platform
- Mapbox
- HERE Technologies

## Next Steps

1. ✅ Hubungi GPS ID support untuk dokumentasi API
2. ✅ Dapatkan contoh request yang benar
3. ✅ Update `GpsIdService` dengan format yang benar
4. ✅ Test ulang dengan `php artisan gps:test`
5. ✅ Implementasi fitur GPS di UI

## Contact

GPS ID Support:
- Website: https://gps.id
- Email: [email support GPS ID]
- Phone: [phone support GPS ID]

## Catatan

File ini akan diupdate setelah mendapat informasi yang benar dari GPS ID support.

Last Updated: 2026-01-14
