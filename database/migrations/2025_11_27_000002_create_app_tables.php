<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // App Users table (separate from admin users)
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->string('profileUrl')->default("");
            $table->string('bio')->nullable();
            $table->string('nid')->nullable();
            $table->integer('points')->default(0);
            $table->bigInteger('registered')->default(0);
            $table->string('status')->default("STANDARD");
            $table->string('fcmToken')->nullable();
            $table->string('accountStatus')->default("ACTIVE");
            $table->integer('totalCalls')->default(0);
            $table->bigInteger('totalDuration')->default(0);
            $table->integer('followers')->default(0);
            $table->string('password');
        });

        // Calls table
        Schema::create('calls', function (Blueprint $table) {
            $table->string('callId')->primary();
            $table->bigInteger('callTime');
            $table->string('callerId');
            $table->string('callerName');
            $table->string('callerProfile')->nullable();
            $table->string('receiverId');
            $table->string('receiverName');
            $table->string('receiverProfile')->nullable();
            $table->bigInteger('duration');
        });

        // User ranks table
        Schema::create('user_ranks', function (Blueprint $table) {
            $table->string('userId')->primary();
            $table->string('name');
            $table->string('profileUrl')->nullable();
            $table->bigInteger('totalDurationLast7Days')->default(0);
            $table->bigInteger('totalDurationLast30Days')->default(0);
            $table->bigInteger('totalLifetimeDuration')->default(0);
            $table->bigInteger('totalCallsLast7Days')->default(0);
            $table->bigInteger('totalCallsLast30Days')->default(0);
            $table->bigInteger('totalLifetimeCalls')->default(0);
            $table->integer('rank')->default(-1);
        });

        // Banners table
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('imageUrl')->default('');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // App configs table
        Schema::create('app_configs', function (Blueprint $table) {
            $table->id();
            $table->string('appStatus')->default('ACTIVE');
            $table->text('appStatusMessage')->nullable();
            $table->boolean('enablePremiumSwitch')->default(false);
            $table->string('facebook')->nullable();
            $table->integer('latestVersion')->default(1);
            $table->text('notice')->nullable();
            $table->string('noticeAction')->nullable();
            $table->string('privacy')->nullable();
            $table->text('reportReasons')->nullable();
            $table->string('stun')->nullable();
            $table->string('support')->nullable();
            $table->text('teacherNotice')->nullable();
            $table->string('terms')->nullable();
            $table->string('turn')->nullable();
            $table->string('turnName')->nullable();
            $table->string('turnPass')->nullable();
            $table->text('updateInfo')->nullable();
            $table->text('warnings')->nullable();
            $table->string('x')->nullable();
            $table->string('youtube')->nullable();
            $table->timestamps();
        });

        // Ratings table
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->string('odcId');
            $table->string('odcName');
            $table->float('rating');
            $table->string('userId');
            $table->text('text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('app_configs');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('user_ranks');
        Schema::dropIfExists('calls');
        Schema::dropIfExists('app_users');
    }
};
