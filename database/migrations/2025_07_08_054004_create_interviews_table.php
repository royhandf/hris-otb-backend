<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('interviews', function (Blueprint $table) {
            $table->uuid('interview_id')->primary();
            $table->uuid('applicant_id');
            $table->uuid('interviewer_id');
            $table->dateTime('schedule');
            $table->enum('result', ['lolos', 'tidak lolos', 'cadangan'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('applicant_id')->references('applicant_id')->on('applicants')->onDelete('cascade');
            $table->foreign('interviewer_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('interviews');
    }
};