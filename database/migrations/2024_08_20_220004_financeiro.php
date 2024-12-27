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
        Schema::create('financeiro', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pedidos_id')->unsigned()->index();
            $table->unsignedBigInteger('clientes_id')->index();
            $table->dateTime('data_transacao');
            $table->decimal('valor_combinado', 15, 2)->nullable();
            $table->string('forma_pagamento_valor_combinado')->nullable();
            $table->dateTime('data_entrada')->nullable();
            $table->dateTime('vencimento')->nullable();
            $table->decimal('valor', 15, 2)->nullable();
            $table->decimal('valor_entrada', 15, 2)->nullable();
            $table->string('forma_pagamento_valor_entrada')->nullable();
            $table->string('status');
            $table->string('conta')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('anexo')->nullable();
            $table->timestamps();

            $table->foreign('pedidos_id')->references('id')->on('pedidos');
            $table->foreign('clientes_id')->references('id')->on('clientes');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financeiro');
    }
};
