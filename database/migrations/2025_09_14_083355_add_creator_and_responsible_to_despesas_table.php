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
        Schema::table('despesas', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->onDelete('cascade');
            
            $table->index(['created_by']);
            $table->index(['responsible_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['responsible_user_id']);
            $table->dropColumn(['created_by', 'responsible_user_id']);
        });
    }
};
