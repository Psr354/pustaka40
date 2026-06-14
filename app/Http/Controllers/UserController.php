<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
        $this->middleware('role:admin');
    }

    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', Rule::in(['admin', 'anggota'])],
            'trashed' => ['nullable', Rule::in(['only'])],
        ]);

        $isArchive = $request->string('trashed')->toString() === 'only';

        $daftarUser = User::query()
            ->with('anggota:id,user_id,nis,nama,kelas')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->string('q')->toString();
                $query->where(function ($builder) use ($keyword) {
                    $builder->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->string('role')->toString());
            })
            ->when($isArchive, fn ($query) => $query->onlyTrashed())
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('user.index', compact('daftarUser', 'isArchive'));
    }

    public function create(): View
    {
        return view('user.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        $this->auditLogService->log('create', 'user', $user->id, null, $user->getAttributes());

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('user.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if ($user->isAdmin() && $validated['role'] !== 'admin' && User::query()->where('role', 'admin')->count() <= 1) {
            return redirect()->route('user.edit', $user)->with('error', 'Minimal harus ada 1 admin aktif.');
        }

        $dataLama = $user->getAttributes();

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        $this->auditLogService->log('update', 'user', $user->id, $dataLama, $user->getAttributes());

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()?->id === (int) $user->id) {
            return redirect()->route('user.index')->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        if ($user->isAdmin() && User::query()->where('role', 'admin')->count() <= 1) {
            return redirect()->route('user.index')->with('error', 'Minimal harus ada 1 admin aktif.');
        }

        $aktif = $user->anggota?->peminjamans()->where('status', 'dipinjam')->exists();

        if ($aktif) {
            return redirect()->route('user.index')->with('error', 'User tidak bisa dihapus karena anggota terkait masih memiliki peminjaman aktif.');
        }

        $dataLama = $user->getAttributes();
        $user->delete();

        $this->auditLogService->log('soft_delete', 'user', $user->id, $dataLama, null);

        return redirect()->route('user.index')->with('success', 'User dipindahkan ke arsip.');
    }

    public function restore(int $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        $this->auditLogService->log('restore', 'user', $user->id, null, $user->getAttributes());

        return redirect()->route('user.index', ['trashed' => 'only'])->with('success', 'User berhasil dipulihkan.');
    }
}
