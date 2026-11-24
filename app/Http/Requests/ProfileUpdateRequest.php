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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            // المعلومات الشخصية الإضافية (user_profiles)
            'bio' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:150'],
            'website' => ['nullable', 'url:http,https', 'max:255'],

            'social_links' => ['nullable', 'array'],
            'social_links.x' => ['nullable', 'url:http,https', 'max:255'],
            'social_links.github' => ['nullable', 'url:http,https', 'max:255'],
            'social_links.instagram' => ['nullable', 'url:http,https', 'max:255'],
            'social_links.linkedin' => ['nullable', 'url:http,https', 'max:255'],
            'social_links.youtube' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }
}
