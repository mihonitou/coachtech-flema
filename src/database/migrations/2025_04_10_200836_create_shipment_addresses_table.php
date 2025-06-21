<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_addresses', function (Blueprint $table) {
            $table->id();
            // ユーザーID（新規追加）
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            // 商品ID（新規追加）
            $table->foreignId('item_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('purchase_id') // 外部キー → purchases(id)
                ->constrained()
                ->onDelete('cascade');


            $table->string('postal_id', 10);     // 郵便番号（NOT NULL）
            $table->text('address');             // 住所（NOT NULL）
            $table->string('building')->nullable(); // 建物名（NULL許可）
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipment_addresses');
    }
}
