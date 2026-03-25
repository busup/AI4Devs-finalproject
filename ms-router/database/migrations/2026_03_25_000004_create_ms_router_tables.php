<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('address', 255)->nullable();
            $table->decimal('lat', 10, 8);
            $table->decimal('lon', 11, 8);
            if (env('DB_CONNECTION') === 'mysql') {
                $table->geometry('location')->nullable(); // ST_Distance_Sphere Support
            }
            $table->string('timezone', 50);
            $table->boolean('is_accessible')->default(false);
            $table->string('approval_status', 50)->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('approval_status');
            $table->index(['lat', 'lon']);
        });

        Schema::create('routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('status', 50)->default('draft');
            $table->uuid('current_snapshot_id')->nullable();
            $table->timestamps();
        });

        Schema::create('route_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('route_id');
            $table->integer('version_number');
            $table->float('total_distance_m')->nullable();
            $table->integer('estimated_duration_s')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->foreign('route_id')->references('id')->on('routes');
            $table->unique(['route_id', 'version_number']);
        });

        Schema::create('route_stops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('route_snapshot_id');
            $table->uuid('stop_id');
            $table->integer('sequence_order');
            $table->integer('dwell_time_s')->default(0);
            $table->string('alias', 255)->nullable();
            $table->boolean('pickup_allowed')->default(true);
            $table->boolean('dropoff_allowed')->default(true);
            $table->boolean('active')->default(true);
            
            $table->foreign('route_snapshot_id')->references('id')->on('route_snapshots');
            $table->foreign('stop_id')->references('id')->on('stops');
        });

        Schema::create('route_geometries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('route_snapshot_id');
            $table->string('geometry_type');
            $table->string('format');
            $table->longText('content');
            $table->string('provider', 50)->nullable();
            $table->integer('segment_index')->nullable();
            $table->integer('accuracy_m')->nullable();
            $table->timestamp('created_at')->nullable();
            
            $table->foreign('route_snapshot_id')->references('id')->on('route_snapshots');
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
        Schema::dropIfExists('route_geometries');
        Schema::dropIfExists('route_stops');
        Schema::dropIfExists('route_snapshots');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('stops');
    }
};
