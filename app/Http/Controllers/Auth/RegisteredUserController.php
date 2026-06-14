<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'not_regex:/^\s*$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'nis' => ['required', 'string', 'max:20', 'regex:/^[0-9]{5,20}$/', 'unique:anggota,nis'],
            'kelas' => ['required', 'string', 'min:2', 'max:20', 'not_regex:/^\s*$/'],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^(?:\\+62|62|0)[0-9]{8,15}$/'],
            'alamat' => ['nullable', 'string', 'max:1000', 'not_regex:/^\s*$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.not_regex' => 'Nama tidak boleh hanya berisi spasi.',
            'nis.regex' => 'NIS hanya boleh angka 5-20 digit.',
            'kelas.not_regex' => 'Kelas tidak boleh hanya berisi spasi.',
            'no_hp.regex' => 'No. HP harus angka valid (contoh: 08123456789 atau +628123456789).',
            'alamat.not_regex' => 'Alamat tidak boleh hanya berisi spasi.',
        ]);

        $user = DB::transaction(function () use ($request): User {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'anggota',
                'password' => Hash::make($request->password),
            ]);

            Anggota::create([
                'user_id' => $user->id,
                'nis' => $request->nis,
                'nama' => $request->name,
                'kelas' => $request->kelas,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('buku.index', absolute: false));
    }
}
