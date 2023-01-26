<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        #  --  Название
        #  --  СсылкаАгента
        #  --  Представление
        #  --  ДанныеДляВизуализации
        Schema::create('agents', function (Blueprint $table) {
            $table->id();

            $table->string('agent_reference');
            $table->string('view')->nullable();

            $table->unique('agent_reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agents');
    }
}
