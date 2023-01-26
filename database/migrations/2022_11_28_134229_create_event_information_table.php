<?php

use App\Models\EventType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # - ПредметСобытия
        # - Значение
        Schema::create('event_information', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('title'); //Для информации. Использовать event_types
            $table->string('event_reference');
            $table->string('event_presentation');
            $table->dateTime('event_time');
            $table->string('state');

            //foreign keys
            //todo: event_states
            $table->foreignIdFor(EventType::class)->constrained();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_information');
    }
}
