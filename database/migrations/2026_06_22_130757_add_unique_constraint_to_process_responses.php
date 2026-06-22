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
        Schema::table('process_responses', function (Blueprint $table) {
            $table->unique('process_id');
        });
    }

    public function down(): void
    {
        Schema::table('process_responses', function (Blueprint $table) {
            $table->dropUnique(['process_id']);
        });
    }
};
