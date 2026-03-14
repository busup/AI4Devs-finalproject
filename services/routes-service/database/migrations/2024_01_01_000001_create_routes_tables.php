<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('timezone')->nullable();
        });

        Schema::create('stop_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
        });

        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('invitation_code')->nullable();
            $table->foreignId('province_id')->constrained('provinces');
            $table->unsignedBigInteger('primary_site_id')->nullable()->comment('Logical ref Sites Svc');
            $table->unsignedTinyInteger('status')->default(0);
            $table->text('polyline_raw')->nullable();
            $table->timestamp('start_timestamp')->nullable();
            $table->timestamp('end_timestamp')->nullable();
            $table->timestamps();
        });

        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->foreignId('stop_type_id')->nullable()->constrained('stop_types');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->string('title')->nullable();
            $table->string('known_title')->nullable();
            $table->unsignedTinyInteger('type_stop')->nullable();
            $table->timestamp('start_timestamp')->nullable();
            $table->timestamp('end_timestamp')->nullable();
            $table->timestamps();
        });

        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->string('time')->nullable();
            $table->text('polyline_raw')->nullable();
            $table->timestamps();
        });

        Schema::create('route_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->foreignId('track_id')->constrained('tracks')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('monday')->default(0);
            $table->unsignedTinyInteger('tuesday')->default(0);
            $table->unsignedTinyInteger('wednesday')->default(0);
            $table->unsignedTinyInteger('thursday')->default(0);
            $table->unsignedTinyInteger('friday')->default(0);
            $table->unsignedTinyInteger('saturday')->default(0);
            $table->unsignedTinyInteger('sunday')->default(0);
            $table->timestamps();
        });

        Schema::create('track_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id')->constrained('tracks')->cascadeOnDelete();
            $table->foreignId('route_stop_id')->constrained('route_stops')->cascadeOnDelete();
            $table->integer('seconds_until_arrival')->nullable();
            $table->integer('seconds_until_departure')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('track_stops');
        Schema::dropIfExists('route_schedules');
        Schema::dropIfExists('tracks');
        Schema::dropIfExists('route_stops');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('stop_types');
        Schema::dropIfExists('provinces');
    }
};
