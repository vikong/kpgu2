<?php

use App\Models\CoordinationType;
use App\Models\EventItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoordinationsTable extends Migration
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
        Schema::create('coordinations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('value')->nullable();
            $table->json('json')->nullable();

            $table->foreignIdFor(CoordinationType::class)->constrained();
            $table->foreignIdFor(EventItem::class)->constrained();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coordinations');
    }
}
