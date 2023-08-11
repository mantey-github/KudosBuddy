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
        Schema::create('kudos_counts', function (Blueprint $table) {
            $table->id();
            $table->string('team_id');
            $table->string('member_id');
            $table->bigInteger('kudos')->default(0);
            $table->unique(['team_id', 'member_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kudos_counts');
    }
};
