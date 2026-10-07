<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$errors = new \Illuminate\Support\ViewErrorBag();
$setting = \Modules\Setting\Entities\SettingPerusahaan::first();

try {
    echo "Rendering view...\n";
    $html = view('setting::perusahaan', ['setting' => $setting, 'errors' => $errors])->render();
    echo "SUCCESS!\n";
    echo "HTML length: " . strlen($html) . "\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
