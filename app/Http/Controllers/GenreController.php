<?php

namespace App\Http\Controllers;

use App\Http\Requests\Genre\StoreGenreRequest;
use App\Http\Requests\Genre\UpdateGenreRequest;
use App\Models\Genre;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GenreController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
        $this->middleware('role:admin');
    }

    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'trashed' => ['nullable', 'in:only'],
        ]);

        $isArchive = $request->string('trashed')->toString() === 'only';

        $daftarGenre = Genre::query()
            ->withCount('bukus')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->string('q')->toString();
                $query->where(function ($builder) use ($keyword) {
                    $builder->where('nama_genre', 'like', "%{$keyword}%")
                        ->orWhere('deskripsi', 'like', "%{$keyword}%");
                });
            })
            ->when($isArchive, fn ($query) => $query->onlyTrashed())
            ->orderBy('nama_genre')
            ->paginate(10)
            ->withQueryString();

        return view('genre.index', compact('daftarGenre', 'isArchive'));
    }

    public function create(): View
    {
        return view('genre.create');
    }

    public function store(StoreGenreRequest $request): RedirectResponse
    {
        $genre = Genre::query()->create($request->validated());
        $this->auditLogService->log('create', 'genre', $genre->id, null, $genre->getAttributes());

        return redirect()->route('genre.index')->with('success', 'Genre berhasil ditambahkan.');
    }

    public function edit(Genre $genre): View
    {
        return view('genre.edit', compact('genre'));
    }

    public function update(UpdateGenreRequest $request, Genre $genre): RedirectResponse
    {
        $dataLama = $genre->getAttributes();
        $genre->update($request->validated());

        $this->auditLogService->log('update', 'genre', $genre->id, $dataLama, $genre->getAttributes());

        return redirect()->route('genre.index')->with('success', 'Genre berhasil diperbarui.');
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        $dataLama = $genre->getAttributes();
        $genre->delete();

        $this->auditLogService->log('soft_delete', 'genre', $genre->id, $dataLama, null);

        return redirect()->route('genre.index')->with('success', 'Genre dipindahkan ke arsip.');
    }

    public function restore(int $id): RedirectResponse
    {
        $genre = Genre::onlyTrashed()->findOrFail($id);
        $genre->restore();

        $this->auditLogService->log('restore', 'genre', $genre->id, null, $genre->getAttributes());

        return redirect()->route('genre.index', ['trashed' => 'only'])->with('success', 'Genre berhasil dipulihkan.');
    }
}
