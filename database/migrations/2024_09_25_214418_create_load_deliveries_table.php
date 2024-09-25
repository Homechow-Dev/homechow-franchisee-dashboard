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
        Schema::create('load_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('FunCode', 10);
            $table->string('MachineID', 20);
            $table->string('TradeNO', 250)->nullable();
            $table->string('SlotNO', 2);
            $table->unsignedMediumInteger('KeyNum')->nullable()->unsigned();
            $table->unsignedTinyInteger('Status')->nullable()->unsigned();
            $table->unsignedSmallInteger('Stock')->nullable()->unsigned();
            $table->unsignedSmallInteger('Capacity')->nullable()->unsigned();
            $table->string('ProductID', 10)->default('HC09876');
            $table->string('Type', 25)->default('HotMeal');
            $table->string('Introduction', 250)->nullable();
            $table->string('Name', 25)->default('Homechow meal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('load_deliveries');
    }
};
