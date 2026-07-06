<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Destination;
use Illuminate\Foundation\Http\FormRequest;

class DestinationWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        $destination = $this->route('destination');

        if (! $destination instanceof Destination) {
            return false;
        }

        $ability = match ($this->route()?->getName()) {
            'dashboard.destinations.submit' => 'submit',
            'dashboard.destinations.review.under-review',
            'dashboard.destinations.review.revision-needed' => 'review',
            'dashboard.destinations.approve' => 'approve',
            'dashboard.destinations.publish' => 'publish',
            'dashboard.destinations.archive' => 'archive',
            'dashboard.destinations.restore-archive' => 'restoreArchive',
            default => null,
        };

        return $ability !== null && ($this->user()?->can($ability, $destination) ?? false);
    }

    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
