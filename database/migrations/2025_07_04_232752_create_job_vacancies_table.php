<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->uuid('vacancy_id')->primary();
            $table->uuid('position_id');
            $table->string('title');
            $table->text('description');
            $table->text('requirements');
            $table->date('deadline');
            $table->timestamps();

            // Foreign key ke tabel positions (kalau tabel positions sudah ada)
            $table->foreign('position_id')->references('position_id')->on('positions');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};