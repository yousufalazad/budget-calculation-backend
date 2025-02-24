<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id()->comment('Primary Key');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('References users table');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade')->comment('References categories table');
            $table->string('title')->nullable()->comment('Title for transaction');
            $table->enum('type', ['income', 'expense'])->comment('Transaction type: income or expense');
            $table->decimal('amount', 10, 2)->comment('Transaction amount');
            $table->foreignId('recurring_type_id')->constrained('recurring_types')->onDelete('cascade')->comment('References recurring type table');
            $table->date('start_date')->comment('Transaction start date');
            $table->string('notes')->nullable()->comment('Transaction notes');
            $table->boolean('is_active')->default(1)->comment('Weather transaction is active or inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
