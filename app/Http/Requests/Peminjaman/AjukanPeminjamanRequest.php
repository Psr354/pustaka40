<?php

namespace App\Http\Requests\Peminjaman;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AjukanPeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAnggota() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'buku_id' => ['required', 'integer', Rule::exists('buku', 'id')->whereNull('deleted_at')],
            'tgl_pinjam' => ['required', 'date', 'after_or_equal:today'],
            'tgl_kembali_rencana' => ['required', 'date', 'after:tgl_pinjam'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'buku_id.required' => 'Buku wajib dipilih.',
            'tgl_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tgl_pinjam.after_or_equal' => 'Tanggal pinjam tidak boleh sebelum hari ini.',
            'tgl_kembali_rencana.required' => 'Tanggal kembali wajib diisi.',
            'tgl_kembali_rencana.after' => 'Tanggal kembali harus setelah tanggal pinjam.',
        ];
    }
}
