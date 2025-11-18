<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Instansi\InstansiDashboardController;
use App\Http\Controllers\Personal\PersonalDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

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

// Dashboard default setelah login - Redirect based on user type
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    // Redirect based on user_type
    switch ($user->user_type) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'instansi':
            return redirect()->route('instansi.dashboard');
        case 'personal':
        default:
            return redirect()->route('personal.dashboard');
    }
})->middleware('auth')->name('dashboard');

// Group untuk Personal User
Route::prefix('personal')->middleware(['auth', 'user.type:personal'])->group(function () {

    // Dashboard Personal
    Route::get('/dashboardPersonal', [PersonalDashboardController::class, 'index'])
        ->name('personal.dashboard');

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

    // Payment Success/Error Pages
    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('personal.payment.success');
    Route::get('/payment/error', [PaymentController::class, 'paymentError'])->name('personal.payment.error');

});

// admin routes
Route::prefix('admin')->middleware(['auth', 'user.type:admin'])->group(function () {

    // Dashboard admin
    Route::get('/dashboardAdmin', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

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
Route::prefix('instansi')->middleware(['auth', 'user.type:instansi'])->group(function () {

    // Dashboard Instansi
    Route::get('/dashboardInstansi', [InstansiDashboardController::class, 'index'])
        ->name('instansi.dashboard');

    // form tes instansi
    Route::get('/formTesInstansi', function () {
        return Inertia::render('Instansi/form-tes-instansi');
    })->name('instansi.form-tes-instansi');
});


require __DIR__ . '/settings.php';
