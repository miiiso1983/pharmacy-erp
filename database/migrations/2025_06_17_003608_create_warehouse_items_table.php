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
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('location')->nullable(); // موقع العنصر في المخزن
            $table->date('last_updated')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // فهرس مركب لضمان عدم تكرار العنصر في نفس المخزن
            $table->unique(['warehouse_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_items');
    }
};
