<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
        ]);

        try {
            $status = Password::sendResetLink($request->only('email'));
        } catch (Throwable $exception) {
            Log::error('Gagal mengirim link reset password.', [
                'email' => $request->string('email')->toString(),
                'message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Link reset belum bisa dikirim. Periksa konfigurasi email aplikasi.']);
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', $this->statusMessage($status))
            : back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $this->statusMessage($status)]);
    }

    private function statusMessage(string $status): string
    {
        return match ($status) {
            Password::RESET_LINK_SENT => 'Link reset password sudah dikirim. Periksa inbox atau folder spam email Anda.',
            Password::INVALID_USER => 'Email belum terdaftar di Pustaka40.',
            Password::RESET_THROTTLED => 'Permintaan terlalu sering. Tunggu sebentar sebelum mencoba lagi.',
            default => 'Link reset password belum bisa dikirim saat ini.',
        };
    }
}
