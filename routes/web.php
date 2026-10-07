<?php
// route for admin
include 'admin.php';

Route::get('/', 'LandingController@index')->name('landing');
Route::get('/layanan', 'LandingController@layanan')->name('landing.layanan');
Route::get('/keunggulan', 'LandingController@keunggulan')->name('landing.keunggulan');
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
