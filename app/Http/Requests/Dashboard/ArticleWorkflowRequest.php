<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;

class ArticleWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        $article = $this->route('article');

        if (! $article instanceof Article) {
            return false;
        }

        $ability = match ($this->route()?->getName()) {
            'dashboard.articles.submit' => 'submit',
            'dashboard.articles.review.under-review',
            'dashboard.articles.review.revision-needed' => 'review',
            'dashboard.articles.approve' => 'approve',
            'dashboard.articles.publish' => 'publish',
            'dashboard.articles.archive' => 'archive',
            'dashboard.articles.restore-archive' => 'restoreArchive',
            default => null,
        };

        return $ability !== null && ($this->user()?->can($ability, $article) ?? false);
    }

    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
