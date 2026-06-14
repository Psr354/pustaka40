<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnggotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->only(['index', 'create', 'store', 'destroy']);
    }

    public function index(Request $request): View
    {
        $query = Anggota::query();

        if ($request->filled('q')) {
            $keyword = $request->string('q')->toString();
            $query->where(function ($builder) use ($keyword) {
                $builder->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('nis', 'like', "%{$keyword}%");
            });
        }

        $daftarAnggota = $query->orderBy('nama')->paginate(10)->withQueryString();

        return view('anggota.index', compact('daftarAnggota'));
    }

    public function create(): View
    {
        return view('anggota.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:anggota,nis',
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string|max:20',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        Anggota::query()->create($validated);

        return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil ditambahkan.');
    }

    public function show(Anggota $anggota): View
    {
        $this->authorizeAnggota($anggota);

        $riwayat = $anggota->peminjaman()->with('buku')->latest()->get();

        return view('anggota.show', compact('anggota', 'riwayat'));
    }

    public function edit(Anggota $anggota): View
    {
        $this->authorizeAnggota($anggota);

        return view('anggota.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota): RedirectResponse
    {
        $this->authorizeAnggota($anggota);

        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:anggota,nis,' . $anggota->id,
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string|max:20',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $anggota->update($validated);

        return redirect()->route('anggota.show', $anggota)->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Anggota $anggota): RedirectResponse
    {
        if ($anggota->peminjaman()->where('status', 'dipinjam')->exists()) {
            return redirect()->route('anggota.index')->with('error', 'Anggota masih memiliki peminjaman aktif.');
        }

        $anggota->delete();

        return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil dihapus.');
    }

    private function authorizeAnggota(Anggota $anggota): void
    {
        $user = auth()->user();

        if ($user && $user->role === 'anggota' && $user->id !== $anggota->id) {
            abort(403, 'Akses ditolak.');
        }
    }
}
