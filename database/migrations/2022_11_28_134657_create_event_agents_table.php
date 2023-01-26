<?php

use App\Models\Agent;
use App\Models\Event;
use App\Models\OwnerType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        #  --  Название
        #  --  Тип {Отправитель = 1, Получатель = 2}
        #  --  ДанныеДляВизуализации
        Schema::create('event_agents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('title');
            $table->integer('type');
            $table->json('view')->nullable();

            $table->foreignIdFor(OwnerType::class)->constrained();
            $table->foreignIdFor(Event::class)->constrained();
            $table->foreignIdFor(Agent::class)->constrained();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_agents');
    }
}
