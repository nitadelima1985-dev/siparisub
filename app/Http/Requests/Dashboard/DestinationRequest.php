<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Destination;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $destination = $this->route('destination');

        if ($destination instanceof Destination) {
            return $this->user()?->can('update', $destination) ?? false;
        }

        return $this->user()?->can('create', Destination::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'destination_category_id' => ['required', 'integer', Rule::exists('destination_categories', 'id')],
            'district_id' => ['required', 'integer', Rule::exists('districts', 'id')],
            'short_description' => ['required', 'string', 'max:1000'],
            'full_description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:2000'],
            'village_name' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'google_maps_url' => ['nullable', 'url', 'max:255'],
            'open_days' => ['nullable', 'string', 'max:255'],
            'open_hours' => ['nullable', 'string', 'max:255'],
            'ticket_adult' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'ticket_child' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'parking_fee' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'tiktok_url' => ['nullable', 'url', 'max:255'],
            'main_attraction' => ['nullable', 'string', 'max:2000'],
            'activities' => ['nullable', 'string', 'max:2000'],
            'best_visit_time' => ['nullable', 'string', 'max:255'],
            'access_notes' => ['nullable', 'string', 'max:2000'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'intent' => ['nullable', Rule::in(['draft', 'submit'])],
        ];
    }
}
