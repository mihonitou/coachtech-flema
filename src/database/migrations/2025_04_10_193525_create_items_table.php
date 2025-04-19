<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id') // 外部キー (users.id)
                ->constrained()
                ->onDelete('cascade');
            $table->string('name', 225);
            $table->text('description');
            $table->integer('price');
            $table->enum('status', ['new', 'used']);
            $table->string('brand_name', 225)->nullable();
            $table->string('image_path', 225);
            $table->boolean('sold');
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
        Schema::dropIfExists('items');
    }
}
