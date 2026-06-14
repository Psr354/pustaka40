<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBuku = Buku::query()->count();
        $totalAnggota = Anggota::query()->count();
        $dipinjam = Peminjaman::query()->where('status', 'dipinjam')->count();
        $totalDenda = Peminjaman::query()->whereMonth('created_at', now()->month)->sum('denda');

        $bukuPopuler = Buku::query()
            ->withCount('peminjaman')
            ->orderByDesc('peminjaman_count')
            ->take(5)
            ->get();

        $terlambat = Peminjaman::query()
            ->where('status', 'dipinjam')
            ->where('tgl_kembali_rencana', '<', now())
            ->with(['buku', 'anggota'])
            ->get();

        return view('dashboard', compact('totalBuku', 'totalAnggota', 'dipinjam', 'totalDenda', 'bukuPopuler', 'terlambat'));
    }
}
