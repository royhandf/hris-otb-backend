<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('contract_id')->primary();
            $table->uuid('employee_id');
            $table->enum('type', ['pkwt', 'pkwtt', 'magang']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['aktif', 'berakhir', 'diperpanjang']);
            $table->text('description')->nullable();
            $table->timestamps();

            // Optional: jika kamu ingin foreign key ke employees table (juga UUID)
            // $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
