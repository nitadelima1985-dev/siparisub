<?php

use App\Enums\WorkflowStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destination_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('destinations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('destination_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('district_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->longText('full_description')->nullable();
            $table->text('address');
            $table->string('village_name')->nullable();
            $table->decimal('latitude', 11, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('google_maps_url')->nullable();
            $table->string('open_days')->nullable();
            $table->string('open_hours')->nullable();
            $table->decimal('ticket_adult', 12, 2)->nullable();
            $table->decimal('ticket_child', 12, 2)->nullable();
            $table->decimal('parking_fee', 12, 2)->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('website_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->text('main_attraction')->nullable();
            $table->text('activities')->nullable();
            $table->string('best_visit_time')->nullable();
            $table->text('access_notes')->nullable();
            $table->enum('workflow_status', WorkflowStatus::values())->default(WorkflowStatus::Draft->value);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('last_content_update_at')->nullable();
            $table->timestamps();

            $table->index(['workflow_status', 'is_active']);
            $table->index(['district_id', 'destination_category_id']);
        });

        Schema::create('destination_media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->enum('media_type', ['image', 'video']);
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['destination_id', 'is_cover', 'sort_order']);
        });

        Schema::create('article_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('article_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_url')->nullable();
            $table->enum('workflow_status', WorkflowStatus::values())->default(WorkflowStatus::Draft->value);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['workflow_status', 'is_active']);
            $table->index(['article_category_id', 'destination_id']);
        });

        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->longText('full_description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('organizer_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('location_name')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('workflow_status', WorkflowStatus::values())->default(WorkflowStatus::Draft->value);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['workflow_status', 'is_active']);
            $table->index(['start_date', 'end_date']);
        });

        Schema::create('approvals', function (Blueprint $table): void {
            $table->id();
            $table->morphs('approvable');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('current_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('current_status', WorkflowStatus::values())->default(WorkflowStatus::Draft->value);
            $table->text('latest_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('current_status');
        });

        Schema::create('approval_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('approval_id')->constrained()->cascadeOnDelete();
            $table->foreignId('acted_by')->constrained('users')->restrictOnDelete();
            $table->enum('from_status', WorkflowStatus::values())->nullable();
            $table->enum('to_status', WorkflowStatus::values());
            $table->text('note')->nullable();
            $table->string('action_type')->nullable();
            $table->timestamps();

            $table->index(['approval_id', 'created_at']);
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('subject');
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('approval_logs');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('events');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('article_categories');
        Schema::dropIfExists('destination_media');
        Schema::dropIfExists('destinations');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('destination_categories');
    }
};
