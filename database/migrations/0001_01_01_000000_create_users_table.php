<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Name of the user');
            $table->integer('azon_id')->unique()->nullable()->comment('Unique ID from an external system');
            $table->enum('type', ['individual', 'superadmin', 'guest'])->default('guest')->comment('Type of user account');
            $table->string('shortname')->nullable()->comment('Shortname of the user');
            $table->string('username')->unique()->nullable()->comment('Unique username for the user');
            $table->string('email')->unique()->comment('Email address');
            $table->uuid('verification_token')->nullable()->unique()->comment('Email verification token');
            $table->timestamp('email_verified_at')->nullable()->comment('Email verification timestamp');
            $table->string('password')->comment('Password hash');
            $table->rememberToken()->comment('Token for "remember me" functionality');
            $table->enum('activation_status', [
                'active', 
                'inactive', 
                'pending', 
                'hold', 
                'under_review', 
                'suspended', 
                'banned',
                'terminated'
            ])->default('active')
              ->comment('User activation status: active, inactive, pending, hold, under review, suspended, or banned. Superadmin can modify if necessary.');

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
