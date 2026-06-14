<?php

namespace App\Http\Requests\Kategori;

use App\Models\Kategori;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriRequest extends FormRequest
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
        /** @var Kategori $kategori */
        $kategori = $this->route('kategori');

        return [
            'nama_kategori' => [
                'required',
                'string',
                'not_regex:/^\s*$/',
                'max:100',
                Rule::in(['Fiksi', 'Nonfiksi']),
                Rule::unique('kategori', 'nama_kategori')->ignore($kategori->id),
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
