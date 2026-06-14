<?php

namespace App\Http\Controllers;

use App\Models\NotifikasiPeminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = NotifikasiPeminjaman::query()
            ->with(['anggota:id,nis,nama', 'peminjaman:id,buku_id,tgl_kembali_rencana,status', 'peminjaman.buku:id,judul']);

        if ($user?->isAnggota()) {
            $anggotaId = $user?->anggota?->id;

            if ($anggotaId) {
                $query->where('anggota_id', $anggotaId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $daftarNotifikasi = $query->latest()->paginate(10);

        return view('notifikasi.index', compact('daftarNotifikasi'));
    }
}

