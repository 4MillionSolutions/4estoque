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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('clientes_id')->unsigned()->index();
            $table->bigInteger('status_id')->unsigned()->index();
            $table->dateTime('data_entrega')->nullable();
            $table->dateTime('data_gerado');
            $table->dateTime('data_entrega_prevista');
            $table->text('observacao')->length(500)->nullable();
            $table->string('status',1);
            $table->timestamps();
            $table->foreign('clientes_id')->references('id')->on('clientes');
            $table->foreign('status_id')->references('id')->on('status');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
};
