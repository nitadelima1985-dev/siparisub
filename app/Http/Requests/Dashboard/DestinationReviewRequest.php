<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\WorkflowStatus;
use App\Models\Destination;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestinationReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $destination = $this->route('destination');

        return $destination instanceof Destination
            && ($this->user()?->can('review', $destination) ?? false);
    }

    public function rules(): array
    {
        return [
            'to_status' => [
                'required',
                Rule::in([
                    WorkflowStatus::UnderReview->value,
                    WorkflowStatus::RevisionNeeded->value,
                    WorkflowStatus::Approved->value,
                    WorkflowStatus::Published->value,
                    WorkflowStatus::Archived->value,
                ]),
            ],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
