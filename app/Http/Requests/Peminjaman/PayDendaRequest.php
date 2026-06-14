<?php

namespace App\Http\Requests\Peminjaman;

use Illuminate\Foundation\Http\FormRequest;

class PayDendaRequest extends FormRequest
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
            'jumlah_bayar' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'catatan_denda' => ['nullable', 'string', 'max:255', 'not_regex:/^\s*$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jumlah_bayar.min' => 'Jumlah bayar minimal 1.',
            'jumlah_bayar.max' => 'Jumlah bayar terlalu besar.',
            'catatan_denda.not_regex' => 'Catatan denda tidak boleh hanya berisi spasi.',
        ];
    }
}
