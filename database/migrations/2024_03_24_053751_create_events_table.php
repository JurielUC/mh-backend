<?php

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
            $table->integer('user_id')->unsigned()->nullable()->index();

            $table->string('status', 30)->nullable();
            $table->string('type', 30,)->nullable();
            $table->string('code', 30)->nullable();

            $table->string('title')->nullable();
            $table->longText('description')->nullable();

            $table->string('date')->nullable();
            $table->string('time')->nullable();

            $table->tinyInteger('deprecated')->default(0);
            $table->timestamps();
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
