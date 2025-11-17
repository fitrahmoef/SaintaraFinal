<?php


use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;

Route::get('/', function () {
    return inertia('landing');
})->name('home');


Route::get('/login', function () {
    return Inertia::render('auth/login'); // Ubah 'Welcome' menjadi 'Home' (sesuai nama file Anda tanpa ekstensi)
})->name('login');

Route::get('/register', function () {
    return Inertia::render('auth/register'); // Ubah 'Welcome' menjadi 'Home' (sesuai nama file Anda tanpa ekstensi)
})->name('register');

Route::get('/calendar', function () {
    return Inertia::render('Calendar'); // Nama 'Calendar' harus sama persis dengan nama file Calendar.tsx
});

// Group untuk Personal User
Route::prefix('personal')->group(function () {

    // Dashboard Personal
    Route::get('/dashboardPersonal', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Dashboard.tsx
        return Inertia::render('Personal/dashboard-personal');
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

    // Form Tes Karakter
    Route::get('/formTes', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Profile.tsx
        return Inertia::render('Personal/form-tes-personal');
    })->name('personal.form-tes');

});

// admin routes
Route::prefix('admin')->group(function () {

    // Dashboard admin
    Route::get('/dashboardAdmin', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Dashboard.tsx
        return Inertia::render('Admin/dashboard-admin');
    })->name('admin.dashboard');

    // Profil Personal
    Route::get('/profileAdmin', function () {
        // Pastikan file ada di: resources/js/Pages/Admin/Profile.tsx
        return Inertia::render('Admin/Profile');
    })->name('admin.profile');

    Route::get('/agendaAdmin', function () {
        // Pastikan file ada di: resources/js/Pages/Admin/Agenda.tsx
        return Inertia::render('Admin/Agenda');
    })->name('admin.agenda');

    Route::get('/penggunaAdmin', function () {
        // Pastikan file ada di: resources/js/Pages/Admin/Pengguna.tsx
        return Inertia::render('Admin/Pengguna');
    })->name('admin.pengguna');

    Route::get('/keuanganAdmin', function () {
        // Pastikan file ada di: resources/js/Pages/Admin/Keuangan.tsx
        return Inertia::render('Admin/Keuangan');
    })->name('admin.keuangan');

    Route::get('/teamAdmin', function () {
        // Pastikan file ada di: resources/js/Pages/Admin/Tim.tsx
        return Inertia::render('Admin/Tim');
    })->name('admin.team');

    Route::get('/supportAdmin', function () {
        // Pastikan file ada di: resources/js/Pages/Admin/Bantuan.tsx
        return Inertia::render('Admin/Bantuan');
    })->name('admin.support');

    Route::get('/settingsAdmin', function () {
        // Pastikan file ada di: resources/js/Pages/Admin/Pengaturan.tsx
        return Inertia::render('Admin/Pengaturan');
    })->name('admin.settings');

});

// instansi routes
Route::prefix('instansi')->group(function () {

    // form tes instansi
    Route::get('/formTesInstansi', function () {
        // Pastikan file ada di: resources/js/Pages/Personal/Dashboard.tsx
        return Inertia::render('Instansi/form-tes-instansi');
    })->name('instansi.form-tes-instansi');
});


require __DIR__ . '/settings.php';
