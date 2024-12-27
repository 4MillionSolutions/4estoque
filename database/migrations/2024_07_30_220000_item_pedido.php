<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_pedidos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pedidos_id')->unsigned()->index();
            $table->text('descricao')->length(500);
            $table->integer('qtde')->length(11);
            $table->dateTime('data_iniciado')->nullable();
            $table->dateTime('data_finalizado')->nullable();
            $table->text('recebedor_entrega')->length(500)->nullable();
            $table->dateTime('data_entrega')->nullable();
            $table->timestamps();

            $table->foreign('pedidos_id')->references('id')->on('pedidos');


        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_pedidos');
    }
};
