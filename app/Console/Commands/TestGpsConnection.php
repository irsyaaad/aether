<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GpsIdService;

class TestGpsConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test GPS ID API connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing GPS ID API Connection...');
        $this->newLine();

        $gpsService = app(GpsIdService::class);

        // Check configuration
        $this->info('1. Checking Configuration...');
        if (!$gpsService->isConfigured()) {
            $this->error('   ✗ GPS ID credentials not configured in .env');
            $this->warn('   Please set GPSID_USERNAME and GPSID_PASSWORD in your .env file');
            return 1;
        }
        $this->info('   ✓ Credentials configured');
        $this->newLine();

        // Test connection
        $this->info('2. Testing Connection...');
        $result = $gpsService->testConnection();
        
        if ($result['success']) {
            $this->info('   ✓ Connection successful');
            $this->info('   IP: ' . ($result['ip'] ?? 'N/A'));
            $this->info('   Status: ' . ($result['status'] ?? 'N/A'));
        } else {
            $this->error('   ✗ Connection failed');
            $this->error('   Message: ' . $result['message']);
            if (isset($result['hint'])) {
                $this->warn('   Hint: ' . $result['hint']);
            }
            return 1;
        }
        $this->newLine();

        // Test token
        $this->info('3. Testing Token Generation...');
        
        // Clear any cached token first
        $gpsService->clearToken();
        
        // Try to get token and show detailed response
        $this->info('   Attempting to get token...');
        $this->info('   URL: ' . config('services.gpsid.base_url') . '/token');
        $this->info('   Username: ' . config('services.gpsid.username'));
        
        $token = $gpsService->getToken();
        
        if ($token) {
            $this->info('   ✓ Token generated successfully');
            $this->info('   Token: ' . substr($token, 0, 20) . '...');
        } else {
            $this->error('   ✗ Failed to generate token');
            $this->warn('   Check storage/logs/laravel.log for detailed error response');
            
            // Show last log entry
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                $logs = file($logFile);
                $lastLogs = array_slice($logs, -10);
                $this->newLine();
                $this->warn('Last log entries:');
                foreach ($lastLogs as $log) {
                    if (strpos($log, 'GPS ID') !== false) {
                        $this->line('   ' . trim($log));
                    }
                }
            }
            
            return 1;
        }
        $this->newLine();

        // Test get vehicles
        $this->info('4. Testing Get Vehicles...');
        $vehicles = $gpsService->getVehicles();
        
        if ($vehicles !== null) {
            $vehicleCount = is_array($vehicles) ? count($vehicles) : 0;
            $this->info('   ✓ Successfully retrieved vehicles');
            $this->info('   Total vehicles: ' . $vehicleCount);
        } else {
            $this->error('   ✗ Failed to get vehicles');
            $this->warn('   Check logs for details');
            return 1;
        }
        $this->newLine();

        $this->info('✓ All tests passed!');
        $this->info('GPS ID API is working correctly.');
        
        return 0;
    }
}
