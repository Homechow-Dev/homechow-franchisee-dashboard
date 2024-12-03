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
        Schema::create('meals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('Cuisine', 100);
            $table->String('Calories', 5);
            $table->String('Category', 25);
            $table->String('Description', 500);
            $table->String('Price', 6);
            $table->String('TotalFat', 50)->nullable();
            $table->String('TotalCarbs', 50)->nullable();
            $table->String('Protien', 50)->nullable();
            $table->String('sodium', 50)->nullable();
            $table->String('MealType', 10);
            $table->String('productID', 10)->nullable();
            $table->String('imageURL')->nullable();
            $table->timestamps();
        });

        Schema::create('kiosk_meal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUlid('kiosk_id');
            $table->foreignId('meal_id');
            $table->String('MachineID');
            $table->String('Total', 3)->nullable();
            $table->String('TotalSold')->nullable();
            $table->String('StockTotal', 3)->nullable();
            $table->String('ProductID', 6)->nullable();
            $table->String('SlotID', 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
        Schema::dropIfExists('kiosk_meal');
    }
};
