# Integrasi GPS ID - Dokumentasi

## ✅ Status: BERHASIL TERINTEGRASI

Sistem telah berhasil terintegrasi dengan GPS ID API.

## Konfigurasi

### 1. Environment Variables
Tambahkan konfigurasi berikut di file `.env`:

```env
GPSID_BASE_URL=https://portal.gps.id/backend/seen/public
GPSID_USERNAME=Persadaaj
GPSID_PASSWORD=PERSADABD17
```

### 2. Database Migration
Migration sudah dijalankan untuk menambahkan kolom `gps_vehicle_id` pada tabel `armada`.

```bash
php artisan migrate
```

### 3. Test Koneksi
Untuk memastikan koneksi GPS ID berfungsi:

```bash
php artisan gps:test
```

Output yang diharapkan:
```
✓ All tests passed!
GPS ID API is working correctly.
```

## API Endpoint GPS ID

### Authentication
**Endpoint:** `POST /login`  
**URL:** `https://portal.gps.id/backend/seen/public/login`

**Request:**
```json
{
  "username": "Persadaaj",
  "password": "PERSADABD17"
}
```

**Response:**
```json
{
  "status": true,
  "message": {
    "data": {
      "username": "persadaaj",
      "fullname": "persada-aj",
      "email": "unairarcely@gmail.com",
      "phone": "628175180087",
      "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
    }
  }
}
```

**Token Location:** `response.message.data.token`

## Fitur

### 1. Mengelola GPS Armada
- **Lokasi**: Menu Armada → Aksi → Kelola GPS
- **Fungsi**: 
  - Melihat daftar kendaraan GPS yang tersedia
  - Mengaitkan armada dengan kendaraan GPS
  - Melepas kaitan armada dengan GPS
  - Melihat status GPS (Terhubung/Belum Terhubung)

### 2. Melihat Lokasi GPS
- **Endpoint**: `GET /ajax/gps/location/{armada_id}`
- **Response**: Data lokasi real-time dari GPS ID API

### 3. Token Management
- Token GPS ID otomatis di-cache selama 23 jam
- Token akan di-refresh otomatis saat expired
- Cache key: `gpsid_token`

## API Endpoints

### Internal API Routes

#### 1. Get GPS Vehicles
```
GET /ajax/gps/vehicles
```
Mengambil daftar semua kendaraan GPS dari GPS ID API.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "vehicle_id",
      "name": "Vehicle Name",
      "plate_number": "B 1234 XYZ"
    }
  ]
}
```

#### 2. Link GPS Vehicle
```
POST /ajax/gps/link
```
Mengaitkan armada dengan kendaraan GPS.

**Request Body:**
```json
{
  "armada_id": 1,
  "gps_vehicle_id": "vehicle_id"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Armada berhasil dikaitkan dengan GPS"
}
```

#### 3. Unlink GPS Vehicle
```
POST /ajax/gps/unlink
```
Melepas kaitan armada dengan GPS.

**Request Body:**
```json
{
  "armada_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "Armada berhasil dilepas dari GPS"
}
```

#### 4. Get GPS Location
```
GET /ajax/gps/location/{armada_id}
```
Mengambil lokasi real-time armada dari GPS.

**Response:**
```json
{
  "success": true,
  "data": {
    "latitude": -6.200000,
    "longitude": 106.816666,
    "speed": 60,
    "timestamp": "2026-01-14 13:00:00"
  }
}
```

## Service Class

### GpsIdService

Service class untuk berinteraksi dengan GPS ID API.

**Location:** `app/Services/GpsIdService.php`

**Methods:**

#### getToken()
Mendapatkan token autentikasi dari GPS ID API. Token di-cache selama 23 jam.

```php
$gpsService = app(\App\Services\GpsIdService::class);
$token = $gpsService->getToken();
```

#### getVehicles()
Mendapatkan daftar semua kendaraan GPS.

```php
$vehicles = $gpsService->getVehicles();
```

#### getVehicle($vehicleId)
Mendapatkan detail kendaraan GPS berdasarkan ID.

```php
$vehicle = $gpsService->getVehicle('vehicle_id');
```

#### getVehicleLocation($vehicleId)
Mendapatkan lokasi real-time kendaraan GPS.

```php
$location = $gpsService->getVehicleLocation('vehicle_id');
```

#### clearToken()
Menghapus token dari cache (untuk testing atau force refresh).

```php
$gpsService->clearToken();
```

## Database Schema

### Tabel: armada

Kolom tambahan:
- `gps_vehicle_id` (string, nullable) - ID kendaraan dari GPS ID API

## UI Components

### GPS Modal
Modal untuk mengelola GPS armada.

**Location:** `Modules/Operasional/Resources/views/armada/gps-modal.blade.php`

**Features:**
- Dropdown untuk memilih kendaraan GPS
- Informasi detail kendaraan GPS
- Tombol untuk mengaitkan/melepas GPS
- Status GPS saat ini

### Armada Index
Tabel armada dengan kolom GPS Status dan tombol Kelola GPS.

**Location:** `Modules/Operasional/Resources/views/armada/index.blade.php`

**Features:**
- Kolom "GPS Status" menampilkan badge (Terhubung/Belum Terhubung)
- Menu dropdown dengan opsi "Kelola GPS"

## Audit Trail

Setiap perubahan GPS (link/unlink) akan tercatat di audit log menggunakan `AuditService`:

```php
AuditService::log(
    'update',
    'armada',
    $armada->id,
    $oldData,
    $armada->toArray()
);
```

## Error Handling

### Token Error
Jika gagal mendapatkan token:
- Error akan di-log ke Laravel log
- Response JSON dengan status 500
- Message: "Gagal mengambil data kendaraan dari GPS ID"

### API Error
Jika API GPS ID error:
- Error akan di-log dengan detail
- Response JSON dengan status 500
- User akan melihat pesan error di SweetAlert

### Validation Error
Jika data tidak valid:
- Laravel validation akan menangani
- Response JSON dengan status 422
- Error message akan ditampilkan

## Testing

### Manual Testing

1. **Test Token Generation:**
```bash
php artisan tinker
>>> $service = app(\App\Services\GpsIdService::class);
>>> $token = $service->getToken();
>>> echo $token;
```

2. **Test Get Vehicles:**
```bash
php artisan tinker
>>> $service = app(\App\Services\GpsIdService::class);
>>> $vehicles = $service->getVehicles();
>>> dd($vehicles);
```

3. **Clear Token Cache:**
```bash
php artisan tinker
>>> $service = app(\App\Services\GpsIdService::class);
>>> $service->clearToken();
```

## Troubleshooting

### Token Tidak Valid
- Periksa username dan password di `.env`
- Clear cache: `php artisan cache:clear`
- Clear token: `$gpsService->clearToken()`

### Tidak Bisa Mengambil Data Kendaraan
- Periksa koneksi internet
- Periksa base URL GPS ID API
- Periksa log Laravel: `storage/logs/laravel.log`

### GPS Status Tidak Update
- Reload halaman armada
- Clear browser cache
- Periksa DataTable ajax reload

## Security

### API Credentials
- Jangan commit file `.env` ke repository
- Gunakan environment variables untuk credentials
- Rotate password secara berkala

### Token Storage
- Token disimpan di cache (bukan database)
- Token expired otomatis setelah 24 jam
- Token tidak di-expose ke frontend

### API Access
- Semua endpoint GPS memerlukan autentikasi Laravel
- Middleware `auth` diterapkan pada semua routes
- CSRF token required untuk POST requests

## Referensi

- **GPS ID API Documentation**: https://gpsid.readme.io/reference
- **Token Endpoint**: https://gpsid.readme.io/reference/token
- **Vehicle Endpoint**: https://gpsid.readme.io/reference/get-vehicle

## Support

Untuk pertanyaan atau masalah terkait integrasi GPS ID, hubungi:
- Email: support@example.com
- Documentation: https://gpsid.readme.io
