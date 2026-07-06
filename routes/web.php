<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Dashboard\ArticleController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\DestinationController;
use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\OrganizationController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Public\PublicArticleController;
use App\Http\Controllers\Public\PublicDestinationController;
use App\Http\Controllers\Public\PublicEventController;
use App\Http\Controllers\Public\PublicMapController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/destinasi', [PublicDestinationController::class, 'index'])->name('public.destinations.index');
Route::get('/destinasi/{slug}', [PublicDestinationController::class, 'show'])->name('public.destinations.show');
Route::get('/event', [PublicEventController::class, 'index'])->name('public.events.index');
Route::get('/event/{slug}', [PublicEventController::class, 'show'])->name('public.events.show');
Route::get('/artikel', [PublicArticleController::class, 'index'])->name('public.articles.index');
Route::get('/artikel/{slug}', [PublicArticleController::class, 'show'])->name('public.articles.show');
Route::get('/peta-wisata', [PublicMapController::class, 'index'])->name('public.map.index');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('dashboard')->name('dashboard.')->group(function (): void {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::resource('organizations', OrganizationController::class)->except(['destroy']);
        Route::resource('users', UserController::class)->except(['destroy']);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::get('/profile/password', [UserController::class, 'password'])->name('profile.password');
        Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password.update');

        Route::resource('destinations', DestinationController::class);
        Route::post('/destinations/{destination}/cover', [DestinationController::class, 'uploadCover'])
            ->name('destinations.cover.store');
        Route::post('/destinations/{destination}/submit', [DestinationController::class, 'submit'])
            ->name('destinations.submit');
        Route::post('/destinations/{destination}/review/under-review', [DestinationController::class, 'markUnderReview'])
            ->name('destinations.review.under-review');
        Route::post('/destinations/{destination}/review/revision-needed', [DestinationController::class, 'requestRevision'])
            ->name('destinations.review.revision-needed');
        Route::post('/destinations/{destination}/approve', [DestinationController::class, 'approve'])
            ->name('destinations.approve');
        Route::post('/destinations/{destination}/publish', [DestinationController::class, 'publish'])
            ->name('destinations.publish');
        Route::post('/destinations/{destination}/archive', [DestinationController::class, 'archive'])
            ->name('destinations.archive');
        Route::post('/destinations/{destination}/restore-archive', [DestinationController::class, 'restoreArchive'])
            ->name('destinations.restore-archive');

        Route::resource('events', EventController::class);
        Route::post('/events/{event}/submit', [EventController::class, 'submit'])
            ->name('events.submit');
        Route::post('/events/{event}/review/under-review', [EventController::class, 'markUnderReview'])
            ->name('events.review.under-review');
        Route::post('/events/{event}/review/revision-needed', [EventController::class, 'requestRevision'])
            ->name('events.review.revision-needed');
        Route::post('/events/{event}/approve', [EventController::class, 'approve'])
            ->name('events.approve');
        Route::post('/events/{event}/publish', [EventController::class, 'publish'])
            ->name('events.publish');
        Route::post('/events/{event}/archive', [EventController::class, 'archive'])
            ->name('events.archive');
        Route::post('/events/{event}/restore-archive', [EventController::class, 'restoreArchive'])
            ->name('events.restore-archive');
        Route::resource('articles', ArticleController::class);
        Route::post('/articles/{article}/submit', [ArticleController::class, 'submit'])
            ->name('articles.submit');
        Route::post('/articles/{article}/review/under-review', [ArticleController::class, 'markUnderReview'])
            ->name('articles.review.under-review');
        Route::post('/articles/{article}/review/revision-needed', [ArticleController::class, 'requestRevision'])
            ->name('articles.review.revision-needed');
        Route::post('/articles/{article}/approve', [ArticleController::class, 'approve'])
            ->name('articles.approve');
        Route::post('/articles/{article}/publish', [ArticleController::class, 'publish'])
            ->name('articles.publish');
        Route::post('/articles/{article}/archive', [ArticleController::class, 'archive'])
            ->name('articles.archive');
        Route::post('/articles/{article}/restore-archive', [ArticleController::class, 'restoreArchive'])
            ->name('articles.restore-archive');
        Route::view('/content', 'dashboard.placeholder', ['title' => 'Manajemen Konten'])
            ->middleware('role:super_admin,admin_humas,konten_kreator,reviewer_akademik')
            ->name('content.index');
    });
});






