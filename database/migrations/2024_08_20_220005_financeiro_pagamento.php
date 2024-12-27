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
        Schema::create('financeiro_pagamento', function (Blueprint $table) {
            $table->id(); // ID Transação
            $table->bigInteger('financeiro_id')->unsigned()->index();
            $table->dateTime('data_pagamento'); // Data da Transação
            $table->decimal('valor_pago', 15, 2); // Valor pago
            $table->integer('forma_pagamento'); // Forma de Pagamento
            $table->timestamps(); // Data de Criação e Última Atualização

            // Foreign key associada ao usuário
            $table->foreign('financeiro_id')->references('id')->on('financeiro');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financeiro_pagamento');
    }
};
