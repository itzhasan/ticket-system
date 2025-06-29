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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_id')->constrained('users');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('priority')->default('Low');
            $table->string('title');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'closed'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
