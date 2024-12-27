<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfis', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();

        });

        DB::table('perfis')->insert(
            [
                [ 'id' => '1','nome' => 'admin'],
                [ 'id' => '2','nome' => 'financeiro'],
                [ 'id' => '3','nome' => 'usuario'],

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
        Schema::dropIfExists('perfis');
    }
};
