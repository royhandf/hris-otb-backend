<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_remove_title_from_job_vacancies_table.php
    public function up()
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->string('title')->nullable(); // jika ingin bisa rollback
        });
    }
};
