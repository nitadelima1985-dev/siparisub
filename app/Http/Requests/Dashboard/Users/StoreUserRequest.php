<?php

namespace App\Http\Requests\Dashboard\Users;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\User::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', Rule::exists('roles', 'id')->where(fn ($query) => $this->user()->hasRole('admin_dinas') ? $query->where('code', '!=', 'super_admin') : $query)],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function validatedUserData(): array
    {
        return [
            ...$this->safe()->except(['password_confirmation', 'profile_photo', 'is_active']),
            'is_active' => $this->boolean('is_active'),
        ];
    }
}

