<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLostFoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lost_founds', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->nullable()->index();

            $table->string('status', 30)->nullable();
            $table->string('type', 30,)->nullable();
            $table->string('code', 30)->nullable();

            $table->string('item_name')->nullable();
            $table->string('location')->nullable();
            $table->string('date_time')->nullable();
            $table->string('finder_name')->nullable();

            $table->longText('image_urls')->nullable();

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
        Schema::dropIfExists('lost_founds');
    }
}
