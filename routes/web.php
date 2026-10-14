<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::post('/chat', [ChatController::class, 'store'])->middleware('throttle:8,1')->name('chat.store');
Route::get('/paket-umrah', [PackageController::class, 'index'])->name('packages.index');
Route::get('/paket-umrah/{package:slug}', [PackageController::class, 'show'])->name('packages.show');
foreach (['tentang-kami' => 'about', 'umrah-jogja' => 'local', 'kontak' => 'contact', 'panduan-umrah' => 'guide', 'privasi' => 'privacy'] as $page => $name) {
    Route::get('/'.$page, [PageController::class, 'show'])->defaults('page', $page)->name($name);
}
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/berita', [ArticleController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [ArticleController::class, 'show'])->name('news.show');
Route::get('/testimoni', [ArticleController::class, 'testimonials'])->name('testimonials');
Route::get('/media/{filename}', function (string $filename) {
    abort_unless(preg_match('/^[a-f0-9-]{36}\.webp$/', $filename), 404);
    abort_unless(Storage::disk('local')->exists('media/'.$filename), 404);

    return response()->file(Storage::disk('local')->path('media/'.$filename), ['Content-Type' => 'image/webp', 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'public, max-age=31536000, immutable']);
})->name('media');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
    Route::middleware(AdminOnly::class)->group(function () {
        Route::get('/', [ContentController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::view('/account', 'admin.account')->name('account');
        Route::put('/account/password', [AuthController::class, 'password'])->middleware('throttle:5,1')->name('password');
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/content/{section}', [ContentController::class, 'index'])->name('content.index');
        Route::get('/content/{section}/create', [ContentController::class, 'create'])->name('content.create');
        Route::post('/content/{section}', [ContentController::class, 'store'])->name('content.store');
        Route::get('/content/{section}/{record}/edit', [ContentController::class, 'edit'])->whereNumber('record')->name('content.edit');
        Route::put('/content/{section}/{record}', [ContentController::class, 'update'])->whereNumber('record')->name('content.update');
        Route::delete('/content/{section}/{record}', [ContentController::class, 'destroy'])->whereNumber('record')->name('content.destroy');
    });
});
Route::get('/robots.txt', function () {
    $rules = config('safir.indexable') ? "Allow: /\nDisallow: /admin\nSitemap: ".route('sitemap') : 'Disallow: /';

    return response("User-agent: *\n".$rules."\n", 200)->header('Content-Type', 'text/plain');
})->name('robots');
Route::get('/{page}', [PageController::class, 'show'])->where('page', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('pages.show');
