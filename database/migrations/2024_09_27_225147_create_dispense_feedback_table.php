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
        Schema::create('dispense_feedback', function (Blueprint $table) {
            $table->id();
            $table->string('FunCode', 10);
            $table->string('MachineID', 20);
            $table->string('TradeNo', 250)->nullable();
            $table->string('SlotNo', 4)->nullable();
            $table->string('PayType')->nullable();
            $table->time('Time')->nullable();
            $table->decimal('Amount', total: 6, places: 1)->default('0.0')->nullable();
            $table->string('ProductID', 10)->default('HC09876')->nullable();
            $table->string('Name', 100)->nullable();
            $table->string('Type', 25)->default('HotMeal')->nullable();
            $table->string('Quantity', 10)->nullable();
            $table->string('Status', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispense_feedback');
    }
};
