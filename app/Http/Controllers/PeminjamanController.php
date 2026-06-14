<?php

namespace App\Http\Controllers;

use App\Http\Requests\Peminjaman\PayDendaRequest;
use App\Mail\PeminjamanDisetujuiMail;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Services\PeminjamanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function __construct(private readonly PeminjamanService $peminjamanService) {}

    public function index(Request $request): View
    {
        $this->sinkronkanDendaTerlambat();

        $query = Peminjaman::query()->with(['buku', 'anggota']);

        if (auth()->user()?->role === 'anggota') {
            $query->where('anggota_id', auth()->user()?->anggota?->id);
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->when($status === 'terlambat', function ($builder) {
                $builder->where(function ($subQuery) {
                    $subQuery->where('status', 'terlambat')
                        ->orWhere(function ($lateQuery) {
                            $lateQuery->where('status', 'dipinjam')
                                ->whereDate('tgl_kembali_rencana', '<', now()->toDateString());
                        });
                });
            }, fn ($builder) => $builder->where('status', $status));
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tgl_pinjam', [
                $request->date('tanggal_mulai')->toDateString(),
                $request->date('tanggal_selesai')->toDateString(),
            ]);
        } elseif ($request->filled('tanggal_mulai')) {
            $query->whereDate('tgl_pinjam', '>=', $request->date('tanggal_mulai')->toDateString());
        } elseif ($request->filled('tanggal_selesai')) {
            $query->whereDate('tgl_pinjam', '<=', $request->date('tanggal_selesai')->toDateString());
        }

        $daftarPeminjaman = $query->latest()->paginate(10)->withQueryString();
        $statusOptions = ['menunggu_acc', 'dipinjam', 'terlambat', 'dikembalikan', 'ditolak'];
        $jumlahFilterAktif = collect($request->only(['q', 'status', 'tanggal_mulai', 'tanggal_selesai']))
            ->filter(fn ($value) => filled($value))
            ->count();
        $ringkasan = $this->ringkasanPeminjaman();

        return view('peminjaman.index', compact('daftarPeminjaman', 'statusOptions', 'jumlahFilterAktif', 'ringkasan'));
    }

    public function create(): View
    {
        $isAdmin = auth()->user()?->role === 'admin';

        $daftarAnggota = $isAdmin
            ? Anggota::query()->orderBy('nama')->get()
            : Anggota::query()->whereKey(auth()->id())->get();

        $daftarBuku = Buku::query()->where('stok', '>', 0)->orderBy('judul')->get();

        return view('peminjaman.create', compact('daftarAnggota', 'daftarBuku', 'isAdmin'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'anggota_id' => [
                'required',
                'exists:anggota,id',
            ],
            'buku_id' => ['required', 'exists:buku,id'],
            'tgl_pinjam' => ['required', 'date'],
            'tgl_kembali_rencana' => ['required', 'date', 'after:tgl_pinjam'],
        ]);

        $anggotaId = (int) $validated['anggota_id'];
        $buku = Buku::query()->findOrFail($validated['buku_id']);

        if ($buku->stok < 1) {
            return back()->withInput()->with('error', 'Stok buku habis.');
        }

        if ($this->punyaPeminjamanAktif($anggotaId, $buku->id)) {
            return back()->withInput()->withErrors(['buku_id' => 'Anggota masih memiliki peminjaman aktif untuk buku ini.']);
        }

        if ($this->jumlahPeminjamanAktif($anggotaId) >= (int) config('perpus.max_pinjaman_aktif', 3)) {
            return back()->withInput()->withErrors(['anggota_id' => 'Anggota sudah mencapai batas peminjaman aktif.']);
        }

        $peminjaman = Peminjaman::query()->create([
            'anggota_id' => $anggotaId,
            'buku_id' => $buku->id,
            'tgl_pinjam' => $validated['tgl_pinjam'],
            'tgl_kembali_rencana' => $validated['tgl_kembali_rencana'],
            'status' => 'dipinjam',
            'denda' => 0,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $buku->decrement('stok');
        $this->kirimEmailDisetujui($peminjaman);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil ditambahkan.');
    }

    public function showAjukanForm(Request $request): View
    {
        $durasiDefault = (int) config('perpus.durasi_default_hari', 7);
        $tanggalPinjamDefault = now()->toDateString();
        $tanggalKembaliDefault = now()->addDays($durasiDefault)->toDateString();
        $preselectedBukuId = $request->input('buku_id');
        $daftarBuku = Buku::query()->where('stok', '>', 0)->orderBy('judul')->get();

        return view('peminjaman.ajukan', compact('daftarBuku', 'preselectedBukuId', 'tanggalPinjamDefault', 'tanggalKembaliDefault', 'durasiDefault'));
    }

    public function ajukan(Request $request): RedirectResponse
    {
        $anggota = $request->user()?->anggota;

        if (! $anggota) {
            return redirect()->route('peminjaman.ajukan.form')->with('error', 'Akun belum tertaut ke data anggota.');
        }

        $validated = $request->validate([
            'buku_id' => ['required', 'exists:buku,id'],
            'tgl_pinjam' => ['required', 'date', 'after_or_equal:today'],
            'tgl_kembali_rencana' => ['required', 'date', 'after:tgl_pinjam'],
        ]);

        $this->peminjamanService->requestPeminjaman([
            'anggota_id' => $anggota->id,
            'buku_id' => $validated['buku_id'],
            'tgl_kembali_rencana' => $validated['tgl_kembali_rencana'],
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Pengajuan peminjaman dikirim dan menunggu ACC admin.');
    }

    public function ajukanDariBuku(Request $request, Buku $buku): RedirectResponse
    {
        return $this->ajukan($request->merge(['buku_id' => $buku->id]));
    }

    public function approve(Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman = $this->peminjamanService->approvePeminjaman($peminjaman, (int) auth()->id());
        $this->kirimEmailDisetujui($peminjaman);

        return redirect()->route('peminjaman.index')->with('success', 'Pengajuan peminjaman disetujui.');
    }

    public function reject(Peminjaman $peminjaman): RedirectResponse
    {
        $this->peminjamanService->rejectPeminjaman($peminjaman, (int) auth()->id(), request('alasan'));

        return redirect()->route('peminjaman.index')->with('success', 'Pengajuan peminjaman ditolak.');
    }

    public function kembalikan(Peminjaman $peminjaman): RedirectResponse
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $this->peminjamanService->kembalikan($peminjaman);

        return redirect()->route('peminjaman.index')->with('success', 'Buku berhasil dikembalikan.');
    }

    public function bayarDenda(PayDendaRequest $request, Peminjaman $peminjaman): RedirectResponse
    {
        $this->peminjamanService->bayarDenda(
            $peminjaman,
            (int) $request->validated('jumlah_bayar'),
            $request->validated('catatan_denda')
        );

        return redirect()->route('peminjaman.index')->with('success', 'Pembayaran denda berhasil dicatat.');
    }

    public function export(Request $request): Response
    {
        $rows = $this->filteredRows($request);
        $lines = ["ID,Tanggal Pinjam,Anggota,Buku,Rencana,Status,Denda,Dibayar,Sisa"];

        foreach ($rows as $item) {
            $lines[] = implode(',', [
                $item->id,
                $item->tgl_pinjam?->toDateString(),
                '"' . str_replace('"', '""', $item->anggota?->nama ?? '-') . '"',
                '"' . str_replace('"', '""', $item->buku?->judul ?? '-') . '"',
                $item->tgl_kembali_rencana?->toDateString(),
                $item->status,
                $item->total_denda,
                $item->denda_dibayar,
                $item->sisa_denda,
            ]);
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-peminjaman.csv"',
        ]);
    }

    public function exportExcel(Request $request): Response
    {
        return $this->export($request);
    }

    public function exportPdf(Request $request): Response
    {
        $rows = $this->filteredRows($request);
        $pdf = Pdf::loadView('peminjaman.export-pdf', compact('rows'));

        return $pdf->download('laporan-peminjaman.pdf');
    }

    public function destroy(Peminjaman $peminjaman): RedirectResponse
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $peminjaman->delete();

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    public function restore(int $id): RedirectResponse
    {
        Peminjaman::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('peminjaman.index', ['trashed' => 'only'])->with('success', 'Data peminjaman berhasil dipulihkan.');
    }

    private function punyaPeminjamanAktif(int $anggotaId, int $bukuId): bool
    {
        return Peminjaman::query()
            ->where('anggota_id', $anggotaId)
            ->where('buku_id', $bukuId)
            ->whereIn('status', ['menunggu_acc', 'dipinjam'])
            ->exists();
    }

    private function jumlahPeminjamanAktif(int $anggotaId): int
    {
        return Peminjaman::query()
            ->where('anggota_id', $anggotaId)
            ->whereIn('status', ['menunggu_acc', 'dipinjam'])
            ->count();
    }

    private function sinkronkanDendaTerlambat(): void
    {
        Peminjaman::query()
            ->where('status', 'dipinjam')
            ->whereDate('tgl_kembali_rencana', '<', now()->toDateString())
            ->get()
            ->each(function (Peminjaman $peminjaman) {
                $peminjaman->forceFill(['denda' => $peminjaman->hari_terlambat * 1000])->save();
            });
    }

    /**
     * @return array<string, int>
     */
    private function ringkasanPeminjaman(): array
    {
        $rows = Peminjaman::query()->get();

        return [
            'total' => $rows->count(),
            'menunggu_acc' => $rows->where('status', 'menunggu_acc')->count(),
            'dipinjam' => $rows->where('status', 'dipinjam')->count(),
            'terlambat' => $rows->filter(fn (Peminjaman $item) => $item->status === 'terlambat' || ($item->status === 'dipinjam' && $item->hari_terlambat > 0))->count(),
            'denda_belum_lunas' => $rows->filter(fn (Peminjaman $item) => $item->sisa_denda > 0)->count(),
            'nominal_denda_tersisa' => $rows->sum(fn (Peminjaman $item) => $item->sisa_denda),
        ];
    }

    private function filteredRows(Request $request)
    {
        return Peminjaman::query()
            ->with(['anggota', 'buku'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->get();
    }

    private function kirimEmailDisetujui(Peminjaman $peminjaman): void
    {
        $peminjaman->loadMissing(['anggota.user', 'buku']);

        if ($peminjaman->anggota?->user?->email) {
            Mail::to($peminjaman->anggota->user->email)->send(new PeminjamanDisetujuiMail($peminjaman));
        }
    }
}
