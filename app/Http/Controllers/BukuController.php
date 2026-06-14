<?php

namespace App\Http\Controllers;

use App\Http\Requests\Buku\StoreBukuRequest;
use App\Http\Requests\Buku\UpdateBukuRequest;
use App\Models\Buku;
use App\Models\Genre;
use App\Models\Kategori;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BukuController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Buku::query()->with(['kategori', 'genre']);

        if ($request->filled('q')) {
            $keyword = $request->string('q')->toString();
            $query->where(function ($builder) use ($keyword) {
                $builder->where('judul', 'like', "%{$keyword}%")
                    ->orWhere('pengarang', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%")
                    ->orWhereHas('kategori', function ($subQuery) use ($keyword) {
                        $subQuery->where('nama_kategori', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('genre', function ($subQuery) use ($keyword) {
                        $subQuery->where('nama_genre', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('kategori_id')) {
            $kategoriId = $request->string('kategori_id')->toString();
            $query->whereHas('kategori', function ($subQuery) use ($kategoriId) {
                $subQuery->where('kategori.id', $kategoriId);
            });
        }

        $daftarBuku = $query->latest()->paginate(10)->withQueryString();
        $daftarKategori = Kategori::query()->orderBy('nama_kategori')->get();

        return view('buku.index', compact('daftarBuku', 'daftarKategori'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $daftarKategori = Kategori::query()->orderBy('nama_kategori')->get();
        $daftarGenre = Genre::query()->orderBy('nama_genre')->get();

        return view('buku.create', compact('daftarKategori', 'daftarGenre'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBukuRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $kategoriIds = $validated['kategori'] ?? [];
        $genreIds = $validated['genre'] ?? [];

        unset($validated['kategori'], $validated['genre'], $validated['cover']);

        $validated['cover_path'] = $request->file('cover')?->store('book-covers', 'public');

        $buku = Buku::query()->create($validated);

        $buku->kategori()->sync($kategoriIds);
        $buku->genre()->sync($genreIds);

        $this->auditLogService->log('create', 'buku', $buku->id, null, $buku->getAttributes());

        return redirect()->route('buku.index')->with('success', 'Data buku berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Buku $buku): View
    {
        $buku->load(['kategori', 'genre']);
        $jumlahDipinjam = $buku->peminjaman()
            ->where('status', 'dipinjam')
            ->count();

        return view('buku.show', compact('buku', 'jumlahDipinjam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buku $buku): View
    {
        $daftarKategori = Kategori::query()->orderBy('nama_kategori')->get();
        $daftarGenre = Genre::query()->orderBy('nama_genre')->get();

        return view('buku.edit', [
            'buku' => $buku,
            'daftarKategori' => $daftarKategori,
            'daftarGenre' => $daftarGenre,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBukuRequest $request, Buku $buku): RedirectResponse
    {
        $validated = $request->validated();
        $kategoriIds = $validated['kategori'] ?? [];
        $genreIds = $validated['genre'] ?? [];
        $dataLama = $buku->getAttributes();

        unset($validated['kategori'], $validated['genre'], $validated['cover']);

        if ($request->hasFile('cover')) {
            if ($buku->cover_path) {
                Storage::disk('public')->delete($buku->cover_path);
            }

            $validated['cover_path'] = $request->file('cover')?->store('book-covers', 'public');
        }

        $buku->update($validated);
        $buku->kategori()->sync($kategoriIds);
        $buku->genre()->sync($genreIds);

        $this->auditLogService->log('update', 'buku', $buku->id, $dataLama, $buku->getAttributes());

        return redirect()->route('buku.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buku $buku): RedirectResponse
    {
        $dataLama = $buku->getAttributes();
        $buku->delete();

        $this->auditLogService->log('soft_delete', 'buku', $buku->id, $dataLama, null);

        return redirect()->route('buku.index')->with('success', 'Data buku dipindahkan ke arsip.');
    }

    public function restore(int $id): RedirectResponse
    {
        $buku = Buku::onlyTrashed()->findOrFail($id);
        $buku->restore();

        $this->auditLogService->log('restore', 'buku', $buku->id, null, $buku->getAttributes());

        return redirect()->route('buku.index', ['trashed' => 'only'])->with('success', 'Data buku berhasil dipulihkan.');
    }
}
