<?php

namespace App\Http\Requests\Peminjaman;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePeminjamanRequest extends FormRequest
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
        return [
            'anggota_id' => ['required', 'integer', Rule::exists('anggota', 'id')->whereNull('deleted_at')],
            'buku_id' => ['required', 'integer', Rule::exists('buku', 'id')->whereNull('deleted_at')],
            'tgl_pinjam' => ['required', 'date'],
            'tgl_kembali_rencana' => ['required', 'date', 'after:tgl_pinjam'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'anggota_id.required' => 'Anggota wajib dipilih.',
            'buku_id.required' => 'Buku wajib dipilih.',
            'tgl_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tgl_kembali_rencana.after' => 'Tanggal kembali rencana harus setelah tanggal pinjam.',
        ];
    }
}
