<?php

use App\Models\Event;
use App\Models\ItemState;
use App\Models\ItemType;
use App\Models\OwnerType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            # Только для информации! Использовать item_type
            $table->string('title');

            $table->foreignIdFor(Event::class)->constrained();
            $table->foreignIdFor(ItemType::class)->constrained();
            $table->foreignIdFor(ItemState::class)->constrained();
            $table->foreignIdFor(OwnerType::class)->constrained();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_items');
    }
}
