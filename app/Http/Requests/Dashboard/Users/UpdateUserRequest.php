<?php

namespace App\Http\Requests\Dashboard\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    public function rules(): array
    {
        $target = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($target)],
            'phone' => ['nullable', 'string', 'max:30'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'role_id' => [$this->user()->hasRole('super_admin', 'admin_dinas') ? 'required' : 'nullable', Rule::exists('roles', 'id')->where(fn ($query) => $this->user()->hasRole('admin_dinas') ? $query->where('code', '!=', 'super_admin') : $query)],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    public function validatedUserData(): array
    {
        $data = $this->safe()->except(['password_confirmation', 'profile_photo', 'is_active']);

        if (! $this->user()->hasRole('super_admin', 'admin_dinas')) {
            unset($data['role_id'], $data['organization_id'], $data['password']);
        }

        if (! filled($data['password'] ?? null)) {
            unset($data['password']);
        }

        if ($this->user()->hasRole('super_admin', 'admin_dinas')) {
            $data['is_active'] = $this->boolean('is_active');
        }

        return $data;
    }
}


