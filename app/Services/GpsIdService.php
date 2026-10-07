<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GpsIdService
{
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = config('services.gpsid.base_url', 'https://api.gpsid.id');
        $this->username = config('services.gpsid.username');
        $this->password = config('services.gpsid.password');
    }

    /**
     * Get authentication token from GPS ID API
     * Token will be cached for 23 hours (valid for 24 hours)
     * 
     * @return string|null
     */
    public function getToken()
    {
        // Check if token exists in cache
        $token = Cache::get('gpsid_token');
        
        if ($token) {
            return $token;
        }

        // Check if credentials are configured
        if (empty($this->username) || empty($this->password)) {
            Log::warning('GPS ID credentials not configured');
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/login', [
                    'username' => $this->username,
                    'password' => $this->password,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // GPS ID returns token in message.data.token structure
                $token = $data['message']['data']['token'] 
                    ?? $data['data']['token'] 
                    ?? $data['token'] 
                    ?? $data['access_token'] 
                    ?? null;

                if ($token) {
                    // Cache token for 23 hours (expires in 24 hours)
                    Cache::put('gpsid_token', $token, now()->addHours(23));
                    Log::info('GPS ID Token generated successfully');
                    return $token;
                }
                
                Log::warning('GPS ID Token not found in response', ['response' => $data]);
            }

            Log::error('GPS ID Token Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $this->baseUrl . '/login',
            ]);

            return null;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('GPS ID Connection Error', [
                'message' => $e->getMessage(),
                'url' => $this->baseUrl,
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('GPS ID Token Exception', [
                'message' => $e->getMessage(),
                'url' => $this->baseUrl
            ]);
            return null;
        }
    }

    /**
     * Get all vehicles from GPS ID API with 5-minute caching
     * 
     * @return array|null Returns array with 'success', 'data', and 'error' keys
     */
    public function getVehicles()
    {
        $cacheKey = 'gpsid_vehicles';
        
        // Try to get cached data first
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            Log::info('GPS ID Get Vehicles - Using cached data');
            return [
                'success' => true,
                'data' => $cachedData,
                'cached' => true
            ];
        }

        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'error' => 'Gagal mendapatkan token GPS ID. Periksa kredensial.'
            ];
        }

        try {
            $response = Http::timeout(10)
                ->retry(2, 100)
                ->withToken($token)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . '/vehicle');

            if ($response->successful()) {
                $data = $response->json();
                
                // Cache the response for 5 minutes
                Cache::put($cacheKey, $data, now()->addMinutes(5));
                
                Log::info('GPS ID Get Vehicles Response - Fresh data cached', ['data' => $data]);
                return [
                    'success' => true,
                    'data' => $data,
                    'cached' => false
                ];
            }

            $errorMessage = 'HTTP ' . $response->status() . ': ' . $response->body();
            Log::error('GPS ID Get Vehicles Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => $errorMessage
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $errorMessage = 'Connection Error: ' . $e->getMessage();
            Log::error('GPS ID Connection Error - Get Vehicles', [
                'message' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $errorMessage
            ];
        } catch (\Exception $e) {
            $errorMessage = 'Exception: ' . $e->getMessage();
            Log::error('GPS ID Get Vehicles Exception', [
                'message' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $errorMessage
            ];
        }
    }

    /**
     * Get specific vehicle by ID from GPS ID API with 5-minute caching
     * 
     * @param string $vehicleId
     * @return array|null
     */
    public function getVehicle($vehicleId)
    {
        $cacheKey = 'gpsid_vehicle_' . $vehicleId;
        
        // Try to get cached data first
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            Log::info('GPS ID Get Vehicle - Using cached data', ['vehicle_id' => $vehicleId]);
            return $cachedData;
        }

        $token = $this->getToken();

        if (!$token) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->retry(2, 100)
                ->withToken($token)
                ->get($this->baseUrl . '/vehicle/' . $vehicleId);

            if ($response->successful()) {
                $data = $response->json();
                
                // Cache the response for 5 minutes
                Cache::put($cacheKey, $data, now()->addMinutes(5));
                
                Log::info('GPS ID Get Vehicle Response - Fresh data cached', ['vehicle_id' => $vehicleId]);
                return $data;
            }

            Log::error('GPS ID Get Vehicle Error', [
                'vehicle_id' => $vehicleId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('GPS ID Connection Error - Get Vehicle', [
                'vehicle_id' => $vehicleId,
                'message' => $e->getMessage()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('GPS ID Get Vehicle Exception', [
                'vehicle_id' => $vehicleId,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get vehicle location/tracking data with 5-minute caching
     * 
     * @param string $vehicleId
     * @return array|null
     */
    public function getVehicleLocation($vehicleId)
    {
        $cacheKey = 'gpsid_vehicle_location_' . $vehicleId;
        
        // Try to get cached data first
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            Log::info('GPS ID Get Vehicle Location - Using cached data', ['vehicle_id' => $vehicleId]);
            return $cachedData;
        }

        $token = $this->getToken();

        if (!$token) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->retry(2, 100)
                ->withToken($token)
                ->get($this->baseUrl . '/vehicle/' . $vehicleId . '/location');

            if ($response->successful()) {
                $data = $response->json();
                
                // Cache the response for 5 minutes
                Cache::put($cacheKey, $data, now()->addMinutes(5));
                
                Log::info('GPS ID Get Vehicle Location Response - Fresh data cached', ['vehicle_id' => $vehicleId]);
                return $data;
            }

            Log::error('GPS ID Get Vehicle Location Error', [
                'vehicle_id' => $vehicleId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('GPS ID Connection Error - Get Location', [
                'vehicle_id' => $vehicleId,
                'message' => $e->getMessage()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('GPS ID Get Vehicle Location Exception', [
                'vehicle_id' => $vehicleId,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Test connection to GPS ID API
     * 
     * @return array
     */
    public function testConnection()
    {
        try {
            // Test DNS resolution
            $host = parse_url($this->baseUrl, PHP_URL_HOST);
            $ip = gethostbyname($host);
            
            if ($ip === $host) {
                return [
                    'success' => false,
                    'message' => 'Cannot resolve DNS for ' . $host,
                    'hint' => 'Check your internet connection or DNS settings'
                ];
            }

            // Test HTTP connection
            $response = Http::timeout(5)->get($this->baseUrl);
            
            return [
                'success' => true,
                'message' => 'Connection successful',
                'ip' => $ip,
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'hint' => 'GPS ID API may be unreachable from this server'
            ];
        }
    }

    /**
     * Check if GPS ID service is configured
     * 
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->username) && !empty($this->password);
    }

    /**
     * Clear cached token (useful for testing or force refresh)
     * 
     * @return void
     */
    public function clearToken()
    {
        Cache::forget('gpsid_token');
    }

    /**
     * Create share location link for live tracking
     * 
     * @param string $imei
     * @param int $expiration Duration in minutes (default 15)
     * @return array|null
     */
    public function createShareLocation($imei, $expiration = 15)
    {
        $token = $this->getToken();

        if (!$token) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/share_location/create_share', [
                    'imei' => $imei,
                    'expiration' => $expiration,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('GPS ID Share Location Created', ['imei' => $imei, 'data' => $data]);
                return $data;
            }

            Log::error('GPS ID Create Share Location Error', [
                'imei' => $imei,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('GPS ID Create Share Location Exception', [
                'imei' => $imei,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get current location data by IMEI with 5-minute caching
     * 
     * @param string $imei
     * @return array|null
     */
    public function getCurrentLocation($imei)
    {
        $cacheKey = 'gpsid_current_location_' . $imei;
        
        // Try to get cached data first
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            Log::info('GPS ID Get Current Location - Using cached data', ['imei' => $imei]);
            return $cachedData;
        }

        $token = $this->getToken();

        if (!$token) {
            return null;
        }

        try {
            // Get all vehicles and find the one with matching IMEI
            $vehicles = $this->getVehicles();
            
            if ($vehicles && isset($vehicles['data'])) {
                $vehicleData = $vehicles['data'];
                
                foreach ($vehicleData as $vehicle) {
                    if (isset($vehicle['imei']) && $vehicle['imei'] === $imei) {
                        $result = [
                            'success' => true,
                            'data' => [
                                'imei' => $vehicle['imei'],
                                'device_name' => $vehicle['device_name'] ?? '',
                                'plate' => $vehicle['plate'] ?? '',
                                'latitude' => $vehicle['latitude'] ?? null,
                                'longitude' => $vehicle['longitude'] ?? null,
                                'speed' => $vehicle['speed'] ?? 0,
                                'angle' => $vehicle['angle'] ?? 0,
                                'address' => $vehicle['address'] ?? '',
                                'last_update' => $vehicle['last_update'] ?? '',
                                'acc' => $vehicle['acc'] ?? 'OFF',
                            ]
                        ];
                        
                        // Cache the result for 5 minutes
                        Cache::put($cacheKey, $result, now()->addMinutes(5));
                        
                        Log::info('GPS ID Get Current Location - Fresh data cached', ['imei' => $imei]);
                        return $result;
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('GPS ID Get Current Location Exception', [
                'imei' => $imei,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get vehicle history report with caching (cache for 10 minutes since historical data changes less frequently)
     * 
     * @param string $imei Device IMEI
     * @param string $startDate Start date (Y-m-d format)
     * @param string $endDate End date (Y-m-d format)
     * @param int $page Page number (default 1)
     * @param int $perPage Items per page (default 25)
     * @return array|null
     */
    public function getVehicleHistory($imei, $startDate, $endDate, $page = 1, $perPage = 25)
    {
        $cacheKey = 'gpsid_vehicle_history_' . md5($imei . $startDate . $endDate . $page . $perPage);
        
        // Try to get cached data first (cache for 10 minutes since historical data changes less frequently)
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            Log::info('GPS ID Get Vehicle History - Using cached data', [
                'imei' => $imei,
                'start' => $startDate,
                'end' => $endDate
            ]);
            return $cachedData;
        }

        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'error' => 'Gagal mendapatkan token GPS ID'
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . '/report/history', [
                    'device' => $imei,
                    'start' => $startDate,
                    'end' => $endDate,
                    'page' => $page,
                    'per_page' => $perPage,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $result = [
                    'success' => true,
                    'data' => $data
                ];
                
                // Cache the result for 10 minutes (historical data changes less frequently)
                Cache::put($cacheKey, $result, now()->addMinutes(10));
                
                Log::info('GPS ID Get Vehicle History Response - Fresh data cached', [
                    'imei' => $imei,
                    'start' => $startDate,
                    'end' => $endDate,
                    'total' => $data['message']['total'] ?? 0
                ]);
                
                return $result;
            }

            $errorMessage = 'HTTP ' . $response->status() . ': ' . $response->body();
            Log::error('GPS ID Get Vehicle History Error', [
                'imei' => $imei,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => $errorMessage
            ];
        } catch (\Exception $e) {
            $errorMessage = 'Exception: ' . $e->getMessage();
            Log::error('GPS ID Get Vehicle History Exception', [
                'imei' => $imei,
                'message' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $errorMessage
            ];
        }
    }
}
