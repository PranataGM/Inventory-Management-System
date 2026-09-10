<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add soft deletes
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Create Audit Logs table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // login, logout, create, update, delete, failed_login
            $table->string('model_type')->nullable(); // e.g. App\Models\Product
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('details')->nullable(); // JSON data of changes
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('audit_logs');
    }
};
