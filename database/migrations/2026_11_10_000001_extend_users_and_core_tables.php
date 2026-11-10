<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('staff')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('company')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('company');
        });

        Schema::create('feature_modules', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('category')->default('core');
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(false);
            $table->boolean('is_core')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip', 12)->nullable();
            $table->boolean('sms_consent')->default(false);
            $table->timestamp('sms_consent_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('specialty')->default('general');
            $table->string('license_number')->nullable();
            $table->string('status')->default('available');
            $table->string('service_zips')->nullable();
            $table->unsignedTinyInteger('daily_capacity')->default(6);
            $table->decimal('rating', 3, 2)->default(5);
            $table->timestamps();
        });

        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('service_type');
            $table->string('priority')->default('normal');
            $table->string('status')->default('intake');
            $table->string('source')->default('phone');
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('zip', 12)->nullable();
            $table->unsignedSmallInteger('estimated_hours')->default(2);
            $table->dateTime('preferred_start')->nullable();
            $table->dateTime('preferred_end')->nullable();
            $table->foreignId('assigned_technician_id')->nullable()->constrained('technicians')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('forecast_code')->unique();
            $table->date('week_start');
            $table->date('week_end');
            $table->string('status')->default('draft');
            $table->unsignedInteger('projected_jobs')->default(0);
            $table->decimal('projected_hours', 8, 1)->default(0);
            $table->decimal('available_hours', 8, 1)->default(0);
            $table->decimal('utilization_pct', 5, 1)->default(0);
            $table->string('surge_risk')->default('normal');
            $table->json('by_service_type')->nullable();
            $table->json('by_zip')->nullable();
            $table->json('daily_breakdown')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('capacity_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_code')->unique();
            $table->foreignId('demand_forecast_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained()->nullOnDelete();
            $table->date('plan_date');
            $table->decimal('planned_hours', 5, 1)->default(8);
            $table->unsignedTinyInteger('planned_jobs')->default(0);
            $table->string('specialty_focus')->nullable();
            $table->string('status')->default('proposed');
            $table->text('ai_recommendation')->nullable();
            $table->text('gap_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('job_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('job_no')->unique();
            $table->foreignId('demand_forecast_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_request_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date')->nullable();
            $table->unsignedSmallInteger('actual_hours')->nullable();
            $table->string('status')->default('scheduled');
            $table->json('checklist')->nullable();
            $table->text('field_notes')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('draft_type');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('title');
            $table->longText('content');
            $table->string('status')->default('pending_approval');
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('summary')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained()->cascadeOnDelete();
            $table->string('event')->default('check_in');
            $table->timestamp('logged_at');
            $table->string('source')->default('mobile');
            $table->string('geo')->nullable();
            $table->timestamps();
        });

        Schema::create('calendar_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('label');
            $table->boolean('enabled')->default(false);
            $table->json('config')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_integrations');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('ai_drafts');
        Schema::dropIfExists('job_tickets');
        Schema::dropIfExists('capacity_plans');
        Schema::dropIfExists('demand_forecasts');
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('technicians');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('feature_modules');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'company', 'is_active']);
        });
    }
};
