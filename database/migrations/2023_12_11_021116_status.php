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
        Schema::create('status', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->boolean('alertacliente');
            $table->string('status',1);
            $table->timestamps();
        });

        DB::table('status')->insert(
            [
                [ 'nome' => 'Pendente', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Em analise', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Concluído', 'alertacliente' => 1, 'status' => 'A'],

            ]
        );
    }


   /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status');
    }
};
