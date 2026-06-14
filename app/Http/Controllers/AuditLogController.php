<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index(Request $request): View
    {
        $daftarLog = AuditLog::query()
            ->with('user:id,name,email')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->string('q')->toString();
                $query->where(function ($builder) use ($keyword) {
                    $builder->where('aksi', 'like', "%{$keyword}%")
                        ->orWhere('entitas', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('audit-log.index', compact('daftarLog'));
    }
}

