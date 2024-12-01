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
        Schema::create('mifis', function (Blueprint $table) {
            $table->id();
            $table->string('MifiId', 50);
            $table->string('Location', 150);
            $table->string('MachineId', 150)->nullable();
            $table->string('SimNumber', 150);
            $table->string('Provider', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mifis');
    }
};
