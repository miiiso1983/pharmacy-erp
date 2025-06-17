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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('city');
            $table->string('area');
            $table->text('address');
            $table->string('phone')->nullable();
            $table->string('manager')->nullable();
            $table->enum('type', ['main', 'branch', 'pharmacy', 'distribution'])->default('branch');
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->decimal('total_value', 15, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
