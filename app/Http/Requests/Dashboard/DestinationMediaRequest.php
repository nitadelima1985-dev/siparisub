<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Destination;
use Illuminate\Foundation\Http\FormRequest;

class DestinationMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $destination = $this->route('destination');

        return $destination instanceof Destination
            && ($this->user()?->can('update', $destination) ?? false);
    }

    public function rules(): array
    {
        return [
            'cover_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'caption' => ['nullable', 'string', 'max:255'],
        ];
    }
}
