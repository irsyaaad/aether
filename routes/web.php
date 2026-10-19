<?php
// route for admin
include 'admin.php';

Route::get('/', 'LandingController@index')->name('landing');
Route::get('/promo', 'LandingController@promo')->name('landing.promo');
Route::get('/layanan', 'LandingController@layanan')->name('landing.layanan');
Route::get('/paket-olah-data', 'LandingController@paketOlahData')->name('landing.paket-olah-data');
Route::get('/jurnal-aether', 'LandingController@jurnalAether')->name('landing.jurnal-aether');
Route::get('/cek-turnitin', 'LandingController@cekTurnitin')->name('landing.cek-turnitin');
Route::get('/workshop-pelatihan', 'LandingController@workshopPelatihan')->name('landing.workshop-pelatihan');
Route::get('/mentoring-penelitian', 'LandingController@mentoringPenelitian')->name('landing.mentoring-penelitian');
Route::get('/mentoring-karir', 'LandingController@mentoringKarir')->name('landing.mentoring-karir');
Route::get('/mentoring-beasiswa', 'LandingController@mentoringBeasiswa')->name('landing.mentoring-beasiswa');
Route::get('/mentoring-lomba', 'LandingController@mentoringLomba')->name('landing.mentoring-lomba');
Route::get('/ai-research', 'LandingController@aiResearch')->name('landing.ai-research');
Route::get('/quiz', 'LandingController@quiz')->name('landing.quiz');
Route::get('/keunggulan', 'LandingController@keunggulan')->name('landing.keunggulan');
Route::get('/testimoni', 'LandingController@testimoni')->name('landing.testimoni');
Route::get('/faq', 'LandingController@faq')->name('landing.faq');
Route::get('/berita', 'LandingController@berita')->name('landing.berita');
Route::get('/berita/{slug}', 'LandingController@beritaDetail')->name('landing.berita.detail');
Route::get('/login', 'Auth\LoginController@showLogin');

Route::get('/forgot-password', 'DashboardController@forgot_password')->name('forgot-password');
Route::post('/forgot-password', 'DashboardController@forgot_password_check')->name('forgot-password');
Route::put('/forgot-password', 'DashboardController@forgot_password_save')->name('forgot-password');
Route::get('/register', 'Auth\RegisterController@showRegister')->name('register');
Route::post('/register', 'Auth\RegisterController@create');
Route::get('/qrcode', 'DashboardController@generateQrCode');
Route::group(['prefix' => '/auth'], function () {
    Route::get('/', 'Auth\LoginController@showLogin')->name('login');
    Route::get('/login', 'Auth\LoginController@showLogin');
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('/logout/{id?}', 'UserController@logout');
});

// SEO Routes
Route::get('/sitemap.xml', function () {
    $articles = \App\Http\Controllers\LandingController::getArticles();
    $baseUrl = rtrim(url('/'), '/');
    $now = date('Y-m-d\TH:i:sP');

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    $pages = [
        ['loc' => $baseUrl . '/', 'changefreq' => 'daily', 'priority' => '1.0'],
        ['loc' => $baseUrl . '/layanan', 'changefreq' => 'weekly', 'priority' => '0.9'],
        ['loc' => $baseUrl . '/keunggulan', 'changefreq' => 'weekly', 'priority' => '0.8'],
        ['loc' => $baseUrl . '/berita', 'changefreq' => 'daily', 'priority' => '0.9'],
    ];

    foreach ($pages as $p) {
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($p['loc']) . '</loc>';
        $xml .= '<lastmod>' . $now . '</lastmod>';
        $xml .= '<changefreq>' . $p['changefreq'] . '</changefreq>';
        $xml .= '<priority>' . $p['priority'] . '</priority>';
        $xml .= '</url>';
    }

    foreach ($articles as $art) {
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($baseUrl . '/berita/' . $art['slug']) . '</loc>';
        $xml .= '<lastmod>' . $now . '</lastmod>';
        $xml .= '<changefreq>monthly</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
});

Route::get('/robots.txt', function () {
    $robotsPath = public_path('robots.txt');
    if (file_exists($robotsPath)) {
        return response(file_get_contents($robotsPath), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
    abort(404);
});

Route::post('/api/cache-landing-css', function (\Illuminate\Http\Request $request) {
    $css = $request->getContent();
    if ($css && strlen($css) > 500) {
        $cssFile = public_path('css/landing-generated.css');
        file_put_contents($cssFile, $css);
        return response()->json(['status' => 'success', 'size' => strlen($css)]);
    }
    return response()->json(['status' => 'ignored']);
});

// Storage fallback route
Route::get('storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');

// Verifikasi Publik E-Sertifikat Pelatihan (Dapat diakses via Scan QR Code)
Route::get('/pelatihan/sertifikat/verifikasi/{identifier?}', [\Modules\Pelatihan\Http\Controllers\PelatihanController::class, 'verifikasiSertifikat'])->name('pelatihan.sertifikat.verifikasi');
Route::get('/verifikasi-sertifikat/{identifier?}', [\Modules\Pelatihan\Http\Controllers\PelatihanController::class, 'verifikasiSertifikat'])->name('verifikasi-sertifikat');

