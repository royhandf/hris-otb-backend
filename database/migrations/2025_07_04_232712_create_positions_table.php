<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->uuid('position_id')->primary();
            $table->string('name');
            $table->decimal('salary', 15, 2)->nullable(); // gaji boleh null?
            $table->string('level')->nullable();          // level juga?
            $table->timestamps();
        });        
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};