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
        Schema::create('homechow_accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id');
            $table->String('Name', 150);
            $table->String('Email');
            $table->String('Phone', 20)->nullable();
            $table->String('Position', 50)->nullable();
            $table->String('Gender', 50)->nullable();
            $table->date('DateofBirth', 50)->nullable();
            $table->String('PresentAddress')->nullable();
            $table->String('PermanentAddress')->nullable();
            $table->String('City')->nullable();
            $table->String('State')->nullable();
            $table->String('Zip')->nullable();
            $table->String('WalletAmount', 10)->nullable();
            $table->String('Status')->nullable();
            $table->String('StripeAccountID')->nullable();
            $table->String('FirstPass');
            $table->String('ImageUrl', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homechow_accounts');
    }
};
