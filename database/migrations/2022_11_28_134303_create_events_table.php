<?php

use App\Models\DocumentTitle;
use App\Models\EventInformation;
use App\Models\Process;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('title');
            
            //foreign keys
            $table->foreignIdFor(DocumentTitle::class)->constrained();
            $table->foreignIdFor(EventInformation::class)->constrained('event_information');
            $table->foreignIdFor(Process::class)->constrained('processes');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
