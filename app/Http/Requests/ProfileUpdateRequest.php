<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255', 'not_regex:/^\s*$/'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'camera_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_profile_photo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.not_regex' => 'Nama tidak boleh hanya berisi spasi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'profile_photo.image' => 'Foto profil dari galeri harus berupa gambar.',
            'profile_photo.mimes' => 'Foto profil dari galeri harus berformat JPG, JPEG, PNG, atau WEBP.',
            'profile_photo.max' => 'Foto profil dari galeri maksimal 2 MB.',
            'camera_photo.image' => 'Foto profil dari kamera harus berupa gambar.',
            'camera_photo.mimes' => 'Foto profil dari kamera harus berformat JPG, JPEG, PNG, atau WEBP.',
            'camera_photo.max' => 'Foto profil dari kamera maksimal 2 MB.',
        ];
    }
}
