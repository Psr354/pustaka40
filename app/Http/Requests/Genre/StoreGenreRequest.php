<?php

namespace App\Http\Requests\Genre;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGenreRequest extends FormRequest
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
            'nama_genre' => [
                'required',
                'string',
                'not_regex:/^\s*$/',
                'max:100',
                Rule::notIn(['Fiksi', 'Nonfiksi']),
                Rule::unique('genre', 'nama_genre'),
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
            'nama_genre.required' => 'Nama genre wajib diisi.',
            'nama_genre.not_in' => 'Genre tidak boleh menggunakan label Fiksi atau Nonfiksi karena itu kategori.',
            'nama_genre.unique' => 'Nama genre sudah ada.',
            'nama_genre.not_regex' => 'Nama genre tidak boleh hanya berisi spasi.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'deskripsi.not_regex' => 'Deskripsi tidak boleh hanya berisi spasi.',
        ];
    }
}
