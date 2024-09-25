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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('FunCode', 10);
            $table->string('MachineID', 20);
            $table->string('Capacity', 10)->default('199');
            $table->string('Content', 250)->nullable();
            $table->string('Coil_id', 3)->nullable();
            $table->string('EnableDiscount', 2)->default('0');
            $table->string('EnableExpire', 2)->default('0');
            $table->string('EnableHot', 2)->default('0');
            $table->string('Extant_quantity', 4)->default('199');
            $table->string('Img_url', 250)->nullable();
            $table->string('LockGoodsCount', 2)->default('0');
            $table->string('Par_name', 100)->nullable();
            $table->decimal('Par_price', total: 6, places: 1)->default('0.0');
            $table->decimal('Sale_price', total: 6, places: 1)->default('0.0');
            $table->string('Work_status', 2)->default('0');
            $table->decimal('dSaleAmount', total: 6, places: 1)->nullable();
            $table->string('discountRate', 2)->default('0');
            $table->dateTime('iExpireTimeStamp')->nullable();
            $table->string('iKeyNum', 10)->default('0');
            $table->string('iSaleNum', 10)->default('0');
            $table->string('iSlotOrder', 10)->default('0');
            $table->string('iSlot_Status', 2)->default('0');
            $table->string('iVerifyAge', 10)->default("0");
            $table->boolean('isInventory')->default(false);
            $table->string('m_AdURL', 250)->nullable();
            $table->string('m_Goods_details_url', 250)->nullable();
            $table->string('m_QrPayUrl', 250)->nullable();
            $table->string('m_iBack', 10)->default('0');
            $table->string('m_iCloseStatus', 2)->default('0');
            $table->string('m_iCol', 2)->default('0');
            $table->string('m_iHeatTime', 2)->default('0');
            $table->string('m_iRow', 3)->default('0');
            $table->string('m_iSlt_hvgs', 3)->default('0');
            $table->string('m_strType', 3)->nullable();
            $table->string('ray', 2)->default('0');
            $table->string('sGoodsCapacity', 3)->nullable();
            $table->string('sGoodsSpec', 3)->nullable();
            $table->string('strGoodsCode', 3)->default('11');
            $table->string('strKeys', 3)->nullable();
            $table->string('strOtherParam1', 3)->default('11');
            $table->string('strOtherParam2', 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
