<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id('contract_id');
            $table->string('employee_id');
            $table->enum('type', ['pkwt', 'pkwtt', 'magang']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['aktif', 'berakhir', 'diperpanjang']);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
