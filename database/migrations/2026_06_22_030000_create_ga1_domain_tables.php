<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('module')->index();
            $table->string('name');
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['module', 'name']);
        });

        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_workflow_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sequence');
            $table->string('role_name');
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            $table->unique(['approval_workflow_id', 'sequence']);
        });

        Schema::create('approval_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_workflow_id')->constrained()->restrictOnDelete();
            $table->morphs('approvable');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->string('status')->default('pending')->index();
            $table->unsignedInteger('current_sequence')->default(1)->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('approval_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_instance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approval_step_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('actor_id')->constrained('users')->restrictOnDelete();
            $table->string('action');
            $table->text('notes')->nullable();
            $table->timestamp('acted_at')->index();
            $table->timestamps();
        });

        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('destination');
            $table->text('purpose');
            $table->date('departure_date')->index();
            $table->date('return_date')->index();
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('travel_realizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_request_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('ticket_cost', 15, 2)->default(0);
            $table->decimal('hotel_cost', 15, 2)->default(0);
            $table->decimal('transport_cost', 15, 2)->default(0);
            $table->decimal('daily_allowance', 15, 2)->default(0);
            $table->decimal('other_cost', 15, 2)->default(0);
            $table->string('evidence_path')->nullable();
            $table->date('realized_at')->nullable();
            $table->timestamps();
        });

        Schema::create('facility_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->unsignedInteger('capacity')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('facility_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->index();
            $table->string('status')->default('draft')->index();
            $table->string('outlook_event_id')->nullable()->index();
            $table->timestamps();
            $table->index(['facility_id', 'starts_at', 'ends_at']);
        });

        Schema::create('atk_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('atk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_category_id')->constrained()->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('minimum_stock')->default(0);
            $table->timestamps();
            $table->index(['stock', 'minimum_stock']);
        });

        Schema::create('atk_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('department')->index();
            $table->text('notes')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('atk_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('atk_item_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
            $table->unique(['atk_request_id', 'atk_item_id']);
        });

        Schema::create('atk_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_item_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->index();
            $table->integer('quantity');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('reorder_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('current_stock');
            $table->unsignedInteger('minimum_stock');
            $table->unsignedInteger('recommended_quantity');
            $table->boolean('is_resolved')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('pic');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('po_number')->unique();
            $table->string('status')->default('draft')->index();
            $table->date('po_date')->index();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->morphs('surveyable');
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('service_type')->index();
            $table->string('status')->default('pending')->index();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('speed_score');
            $table->unsignedTinyInteger('service_score');
            $table->unsignedTinyInteger('satisfaction_score');
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('channel')->index();
            $table->string('title');
            $table->text('body');
            $table->string('teams_webhook_url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('report_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('report_type')->index();
            $table->string('format');
            $table->json('filters')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        Schema::create('login_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('successful')->default(false)->index();
            $table->timestamp('logged_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'login_activities', 'report_exports', 'system_notifications', 'survey_responses', 'surveys',
            'purchase_order_items', 'purchase_orders', 'vendors', 'reorder_alerts', 'atk_stock_movements',
            'atk_request_items', 'atk_requests', 'atk_items', 'atk_categories', 'facility_bookings',
            'facilities', 'facility_categories', 'travel_realizations', 'travel_requests',
            'approval_actions', 'approval_instances', 'approval_steps', 'approval_workflows',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
