<?php


use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;

Route::get('/', function () {
    return Inertia::render('landing'); // Ubah 'Welcome' menjadi 'Home' (sesuai nama file Anda tanpa ekstensi)
});

Route::get('/calendar', function () {
    return Inertia::render('Calendar'); // Nama 'Calendar' harus sama persis dengan nama file Calendar.tsx
});

// Group untuk Personal User
Route::prefix('personal')->group(function () {

    // Dashboard Personal
    Route::get('/dashboardPersonal', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Dashboard.tsx
        return Inertia::render('Personal/dashboard');
    })->name('personal.dashboard');

    // Profil Personal
    Route::get('/profilePersonal', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Profile.tsx
        return Inertia::render('Personal/Profile');
    })->name('personal.profile');

    // Daftar Tes Karakter
    Route::get('/daftarTes', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Profile.tsx
        return Inertia::render('Personal/daftar-tes');
    })->name('personal.daftar-tes');

    // Transaksi dan Token
    Route::get('/transaksiToken', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Profile.tsx
        return Inertia::render('Personal/transaksi-token');
    })->name('personal.transaksi-token');

    // Transaksi dan Token
    Route::get('/hasilTes', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Profile.tsx
        return Inertia::render('Personal/results');
    })->name('personal.results');

    // Hadiah dan Donasi
    Route::get('/hadiahDonasi', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Profile.tsx
        return Inertia::render('Personal/hadiah-donasi');
    })->name('personal.hadiah-donasi');

    // Bantuan dan Layanan
    Route::get('/bantuan', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Profile.tsx
        return Inertia::render('Personal/bantuan');
    })->name('personal.bantuan');

    // Settings
    Route::get('/setting', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Profile.tsx
        return Inertia::render('Personal/setting');
    })->name('personal.setting');

});


require __DIR__ . '/settings.php';
