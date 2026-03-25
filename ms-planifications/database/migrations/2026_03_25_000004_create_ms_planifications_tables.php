<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expeditions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->uuid('route_snapshot_ref_id');
            $table->integer('days_of_week');
            $table->time('base_time');
            $table->string('status', 50)->default('draft');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expedition_stops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('expedition_id');
            $table->uuid('stop_logical_id');
            $table->integer('sequence_order');
            $table->integer('offset_seconds')->default(0);
            $table->boolean('active')->default(true);
            $table->boolean('pickup_allowed')->default(true);
            $table->boolean('dropoff_allowed')->default(true);

            $table->foreign('expedition_id')->references('id')->on('expeditions');
        });

        Schema::create('capacity_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('expedition_id');
            $table->integer('max_seats');
            $table->json('segment_rules')->nullable();
            $table->json('client_restrictions')->nullable();
            $table->boolean('active')->default(true);

            $table->foreign('expedition_id')->references('id')->on('expeditions');
        });

        Schema::create('allocation_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('expedition_id');
            $table->string('rule_type', 50);
            $table->json('parameters')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('active')->default(true);

            $table->foreign('expedition_id')->references('id')->on('expeditions');
        });

        Schema::create('planifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('expedition_id');
            $table->date('date_from');
            $table->date('date_until');
            $table->json('exceptions')->nullable();
            $table->json('non_working_days')->nullable();
            $table->string('status', 50)->default('active');
            $table->timestamps();

            $table->foreign('expedition_id')->references('id')->on('expeditions');
        });

        Schema::create('plan_generation_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('planification_id');
            $table->string('status', 50)->default('pending');
            $table->integer('retries')->default(0);
            $table->text('error_message')->nullable();
            $table->json('results')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('planification_id')->references('id')->on('planifications');
        });

        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('planification_id');
            $table->uuid('route_snapshot_ref_id');
            $table->date('service_date');
            $table->time('departure_time');
            $table->integer('capacity');
            $table->string('status', 50)->default('confirmed');
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();

            $table->foreign('planification_id')->references('id')->on('planifications');
        });

        Schema::create('service_stops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id');
            $table->uuid('stop_logical_id');
            $table->integer('sequence_order');
            $table->time('scheduled_time');
            $table->boolean('active')->default(true);
            $table->boolean('pickup_allowed')->default(true);
            $table->boolean('dropoff_allowed')->default(true);

            $table->foreign('service_id')->references('id')->on('services');
        });

        Schema::create('service_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id');
            $table->uuid('vehicle_logical_id')->nullable();
            $table->uuid('driver_logical_id')->nullable();
            $table->string('assignment_status', 50)->default('draft');
            $table->string('provider')->nullable();
            $table->text('decision_reason')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('service_id')->references('id')->on('services');
        });

        Schema::create('outbox_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_type');
            $table->string('aggregate_type');
            $table->uuid('aggregate_id');
            $table->json('payload');
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbox_events');
        Schema::dropIfExists('service_assignments');
        Schema::dropIfExists('service_stops');
        Schema::dropIfExists('services');
        Schema::dropIfExists('plan_generation_jobs');
        Schema::dropIfExists('planifications');
        Schema::dropIfExists('allocation_rules');
        Schema::dropIfExists('capacity_rules');
        Schema::dropIfExists('expedition_stops');
        Schema::dropIfExists('expeditions');
    }
};
