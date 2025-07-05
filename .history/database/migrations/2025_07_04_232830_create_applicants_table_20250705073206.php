<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->uuid('applicant_id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('cv_file')->nullable(); // nama file CV (opsional)
            $table->uuid('applied_position_id');   // foreign key ke job_vacancies
            $table->enum('status', ['applied', 'interview', 'hired', 'rejected'])->default('applied');
            $table->timestamps();

            // Foreign key
            $table->foreign('applied_position_id')->references('vacancy_id')->on('job_vacancies');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};