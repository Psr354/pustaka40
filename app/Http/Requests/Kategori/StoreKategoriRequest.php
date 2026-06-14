<?php

namespace App\Http\Requests\Kategori;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKategoriRequest extends FormRequest
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
            'nama_kategori' => [
                'required',
                'string',
                'not_regex:/^\s*$/',
                'max:100',
                Rule::in(['Fiksi', 'Nonfiksi']),
                Rule::unique('kategori', 'nama_kategori'),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1000', 'not_regex:/^\s*$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.in' => 'Kategori hanya boleh Fiksi atau Nonfiksi.',
            'nama_kategori.unique' => 'Nama kategori sudah ada.',
            'nama_kategori.not_regex' => 'Nama kategori tidak boleh hanya berisi spasi.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'deskripsi.not_regex' => 'Deskripsi tidak boleh hanya berisi spasi.',
        ];
    }
}
