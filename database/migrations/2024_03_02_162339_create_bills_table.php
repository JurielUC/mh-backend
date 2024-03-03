<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();

            $table->string('status', 30)->nullable();
            $table->string('type', 30,)->nullable();
            $table->string('code', 30)->nullable();

            $table->string('name')->nullable();
            $table->longText('description')->nullable();

            $table->decimal('price', 10, 2)->nullable();

            $table->string('date')->nullable();
            $table->string('due')->nullable();

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
        Schema::dropIfExists('bills');
    }
}
