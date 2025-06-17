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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('company_name')->nullable()->after('address');
            $table->string('tax_number')->nullable()->after('company_name');
            $table->enum('user_type', ['admin', 'customer', 'employee'])->default('customer')->after('tax_number');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('user_type');
            $table->text('notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'company_name', 'tax_number', 'user_type', 'status', 'notes']);
        });
    }
};
