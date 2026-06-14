<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/', function () {
    return view('welcome');
})->withoutMiddleware([StartSession::class, ShareErrorsFromSession::class, VerifyCsrfToken::class])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin')
        ->name('dashboard');

    Route::resource('buku', BukuController::class);
    Route::post('buku/{id}/restore', [BukuController::class, 'restore'])
        ->name('buku.restore')
        ->middleware('role:admin');
    Route::post('buku/{buku}/ajukan-peminjaman', [PeminjamanController::class, 'ajukanDariBuku'])
        ->middleware('role:anggota')
        ->name('buku.ajukan-peminjaman');

    Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('peminjaman/ajukan/form', [PeminjamanController::class, 'showAjukanForm'])
        ->middleware('role:anggota')
        ->name('peminjaman.ajukan.form');
    Route::post('peminjaman/ajukan', [PeminjamanController::class, 'ajukan'])
        ->middleware('role:anggota')
        ->name('peminjaman.ajukan');
    Route::get('notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');

    Route::middleware('role:admin')->group(function () {
        Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
        Route::resource('user', UserController::class)->except(['show']);
        Route::post('user/{id}/restore', [UserController::class, 'restore'])->name('user.restore');

        Route::resource('kategori', KategoriController::class)->except(['show']);
        Route::post('kategori/{id}/restore', [KategoriController::class, 'restore'])->name('kategori.restore');

        Route::resource('genre', GenreController::class)->except(['show']);
        Route::post('genre/{id}/restore', [GenreController::class, 'restore'])->name('genre.restore');

        Route::resource('anggota', AnggotaController::class)
            ->parameters(['anggota' => 'anggota']);
        Route::post('anggota/{id}/restore', [AnggotaController::class, 'restore'])->name('anggota.restore');

        Route::get('peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::get('peminjaman/export', [PeminjamanController::class, 'export'])->name('peminjaman.export');
        Route::get('peminjaman/export-excel', [PeminjamanController::class, 'exportExcel'])->name('peminjaman.export.excel');
        Route::get('peminjaman/export-pdf', [PeminjamanController::class, 'exportPdf'])->name('peminjaman.export.pdf');
        Route::post('peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::post('peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve'])
            ->name('peminjaman.approve');
        Route::post('peminjaman/{peminjaman}/reject', [PeminjamanController::class, 'reject'])
            ->name('peminjaman.reject');
        Route::post('peminjaman/{peminjaman}/bayar-denda', [PeminjamanController::class, 'bayarDenda'])
            ->name('peminjaman.bayar-denda');
        Route::post('peminjaman/{id}/restore', [PeminjamanController::class, 'restore'])
            ->name('peminjaman.restore');
        Route::delete('peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    });

    Route::post('peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])
        ->middleware('role:admin')
        ->name('peminjaman.kembalikan');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
