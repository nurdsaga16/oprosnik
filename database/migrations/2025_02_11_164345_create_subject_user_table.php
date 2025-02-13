<?php

declare(strict_types=1);

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
        Schema::create('subject_user', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->primary(['subject_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_users');
    }
};
