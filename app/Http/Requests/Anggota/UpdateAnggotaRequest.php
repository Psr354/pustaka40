<?php

namespace App\Http\Requests\Anggota;

use App\Models\Anggota;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Anggota $anggota */
        $anggota = $this->route('anggota');

        return [
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'anggota')->whereNull('deleted_at'),
                Rule::unique('anggota', 'user_id')->ignore($anggota->id),
            ],
            'nis' => ['required', 'string', 'max:20', 'regex:/^[0-9]{5,20}$/', Rule::unique('anggota', 'nis')->ignore($anggota->id)],
            'nama' => ['required', 'string', 'min:3', 'max:100', 'not_regex:/^\s*$/'],
            'kelas' => ['required', 'string', 'min:2', 'max:20', 'not_regex:/^\s*$/'],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^(?:\\+62|62|0)[0-9]{8,15}$/'],
            'alamat' => ['nullable', 'string', 'max:1000', 'not_regex:/^\s*$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.unique' => 'User ini sudah terhubung ke anggota lain.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.regex' => 'NIS hanya boleh angka 5-20 digit.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.not_regex' => 'Nama tidak boleh hanya berisi spasi.',
            'kelas.required' => 'Kelas wajib diisi.',
            'kelas.not_regex' => 'Kelas tidak boleh hanya berisi spasi.',
            'no_hp.regex' => 'No. HP harus angka valid (contoh: 08123456789 atau +628123456789).',
            'alamat.not_regex' => 'Alamat tidak boleh hanya berisi spasi.',
        ];
    }
}
