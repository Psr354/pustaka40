<?php

namespace App\Http\Requests\Buku;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'min:2', 'max:255', 'not_regex:/^\s*$/'],
            'pengarang' => ['required', 'string', 'min:2', 'max:255', 'not_regex:/^\s*$/'],
            'deskripsi' => ['nullable', 'string', 'max:2000', 'not_regex:/^\s*$/'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'tahun_terbit' => ['required', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y')],
            'stok' => ['required', 'integer', 'min:0', 'max:100000'],
            'kategori' => ['required', 'array', 'size:1'],
            'kategori.*' => ['integer', 'distinct', Rule::exists('kategori', 'id')->whereNull('deleted_at')],
            'genre' => ['nullable', 'array'],
            'genre.*' => ['integer', 'distinct', Rule::exists('genre', 'id')->whereNull('deleted_at')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.not_regex' => 'Judul tidak boleh hanya berisi spasi.',
            'pengarang.not_regex' => 'Pengarang tidak boleh hanya berisi spasi.',
            'deskripsi.max' => 'Deskripsi maksimal 2000 karakter.',
            'deskripsi.not_regex' => 'Deskripsi tidak boleh hanya berisi spasi.',
            'cover.image' => 'Cover harus berupa gambar.',
            'cover.mimes' => 'Cover harus berformat JPG, JPEG, PNG, atau WEBP.',
            'cover.max' => 'Ukuran cover maksimal 2 MB.',
            'stok.max' => 'Stok maksimal 100000.',
            'kategori.size' => 'Kategori wajib dipilih tepat 1.',
        ];
    }
}
