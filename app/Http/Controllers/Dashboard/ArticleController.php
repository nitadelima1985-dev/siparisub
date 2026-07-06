<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\WorkflowStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ArticleRequest;
use App\Http\Requests\Dashboard\ArticleWorkflowRequest;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Destination;
use App\Services\ArticleWorkflowService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Article::class);

        $articles = Article::query()
            ->with(['category', 'destination', 'creator'])
            ->when(! $request->user()->hasRole('super_admin', 'admin_dinas', 'reviewer_akademik'), function ($query) use ($request): void {
                $query->where('created_by', $request->user()->id);
            })
            ->when($request->filled('status'), fn ($query) => $query->where('workflow_status', $request->input('status')))
            ->when($request->filled('category'), fn ($query) => $query->where('article_category_id', $request->integer('category')))
            ->when($request->filled('destination'), fn ($query) => $query->where('destination_id', $request->integer('destination')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.articles.index', [
            'articles' => $articles,
            'categories' => $this->categories(),
            'destinations' => $this->destinations(),
            'statuses' => WorkflowStatus::cases(),
            'reviewQueueStatuses' => [WorkflowStatus::Submitted, WorkflowStatus::UnderReview, WorkflowStatus::RevisionNeeded],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Article::class);

        return view('dashboard.articles.create', [
            'article' => new Article([
                'workflow_status' => WorkflowStatus::Draft,
                'is_active' => true,
                'is_featured' => false,
            ]),
            'categories' => $this->categories(),
            'destinations' => $this->destinations(),
        ]);
    }

    public function store(ArticleRequest $request, ArticleWorkflowService $workflow): RedirectResponse
    {
        $data = $this->articleData($request);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('articles/featured', 'public');
        }

        $article = DB::transaction(function () use ($request, $data): Article {
            return Article::create([
                ...$data,
                'created_by' => $request->user()->id,
                'slug' => $this->uniqueSlug($data['title']),
                'workflow_status' => WorkflowStatus::Draft,
            ]);
        });

        if ($request->input('intent') === 'submit' && $request->user()->can('submit', $article)) {
            $workflow->submit($article, $request->user());

            return redirect()->route('dashboard.articles.show', $article)->with('success', 'Artikel berhasil dibuat dan disubmit untuk review.');
        }

        return redirect()->route('dashboard.articles.show', $article)->with('success', 'Draft artikel berhasil disimpan.');
    }

    public function show(Article $article): View
    {
        $this->authorize('view', $article);

        $article->load([
            'category',
            'destination',
            'creator',
            'updater',
            'approver',
            'latestApproval',
            'approvals.logs.actor',
        ]);

        return view('dashboard.articles.show', [
            'article' => $article,
            'approvalLogs' => $article->approvals->flatMap->logs->sortByDesc('created_at'),
        ]);
    }

    public function edit(Article $article): View
    {
        $this->authorize('update', $article);

        return view('dashboard.articles.edit', [
            'article' => $article,
            'categories' => $this->categories(),
            'destinations' => $this->destinations(),
        ]);
    }

    public function update(ArticleRequest $request, Article $article, ArticleWorkflowService $workflow): RedirectResponse
    {
        $data = $this->articleData($request);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('articles/featured', 'public');
        }

        DB::transaction(function () use ($request, $article, $data): void {
            if ($article->title !== $data['title']) {
                $data['slug'] = $this->uniqueSlug($data['title'], $article->id);
            }

            $article->update([
                ...$data,
                'updated_by' => $request->user()->id,
            ]);
        });

        if ($request->input('intent') === 'submit' && $request->user()->can('submit', $article)) {
            $workflow->submit($article, $request->user());

            return redirect()->route('dashboard.articles.show', $article)->with('success', 'Artikel berhasil diperbarui dan disubmit untuk review.');
        }

        return redirect()->route('dashboard.articles.show', $article)->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete', $article);

        DB::transaction(function () use ($article): void {
            $article->load('approvals.logs');

            foreach ($article->approvals as $approval) {
                $approval->logs()->delete();
                $approval->delete();
            }

            $article->activityLogs()->delete();

            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }

            $article->delete();
        });

        return redirect()
            ->route('dashboard.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    public function submit(ArticleWorkflowRequest $request, Article $article, ArticleWorkflowService $workflow): RedirectResponse
    {
        $workflow->submit($article, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.articles.show', $article)->with('success', 'Artikel berhasil disubmit untuk review.');
    }

    public function markUnderReview(ArticleWorkflowRequest $request, Article $article, ArticleWorkflowService $workflow): RedirectResponse
    {
        $workflow->markUnderReview($article, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.articles.show', $article)->with('success', 'Status artikel diubah menjadi under review.');
    }

    public function requestRevision(ArticleWorkflowRequest $request, Article $article, ArticleWorkflowService $workflow): RedirectResponse
    {
        $workflow->requestRevision($article, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.articles.show', $article)->with('success', 'Catatan revisi artikel berhasil dikirim.');
    }

    public function approve(ArticleWorkflowRequest $request, Article $article, ArticleWorkflowService $workflow): RedirectResponse
    {
        $workflow->approve($article, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.articles.show', $article)->with('success', 'Artikel berhasil di-approve.');
    }

    public function publish(ArticleWorkflowRequest $request, Article $article, ArticleWorkflowService $workflow): RedirectResponse
    {
        $workflow->publish($article, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.articles.show', $article)->with('success', 'Artikel berhasil dipublish.');
    }

    public function archive(ArticleWorkflowRequest $request, Article $article, ArticleWorkflowService $workflow): RedirectResponse
    {
        $workflow->archive($article, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.articles.show', $article)->with('success', 'Artikel berhasil diarsipkan.');
    }

    public function restoreArchive(ArticleWorkflowRequest $request, Article $article, ArticleWorkflowService $workflow): RedirectResponse
    {
        $this->authorize('restoreArchive', $article);

        $workflow->restoreArchive($article, $request->user(), $request->input('note'));

        return redirect()
            ->route('dashboard.articles.show', $article)
            ->with('success', 'Arsip artikel berhasil dibuka kembali.');
    }

    private function articleData(ArticleRequest $request): array
    {
        return [
            ...$request->safe()->except(['intent', 'featured_image', 'is_featured', 'is_active']),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (Article::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function categories()
    {
        return ArticleCategory::query()->where('is_active', true)->orderBy('name')->get();
    }

    private function destinations()
    {
        return Destination::query()->orderBy('name')->get(['id', 'name']);
    }
}
