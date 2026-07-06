<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class EventWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');

        if (! $event instanceof Event) {
            return false;
        }

        $ability = match ($this->route()?->getName()) {
            'dashboard.events.submit' => 'submit',
            'dashboard.events.review.under-review',
            'dashboard.events.review.revision-needed' => 'review',
            'dashboard.events.approve' => 'approve',
            'dashboard.events.publish' => 'publish',
            'dashboard.events.archive' => 'archive',
            'dashboard.events.restore-archive' => 'restoreArchive',
            default => null,
        };

        return $ability !== null && ($this->user()?->can($ability, $event) ?? false);
    }

    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
