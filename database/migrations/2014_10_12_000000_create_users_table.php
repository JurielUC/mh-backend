<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();

            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('gender')->nullable();

            $table->string('email')->unique();
            $table->string('password')->nullable();
            
            $table->string('phone')->nullable();
            $table->string('house_no')->nullable();
            $table->string('street')->nullable();
            $table->string('house_type')->nullable();

            $table->string('role')->nullable();
            $table->string('image')->nullable();

            $table->tinyInteger('deprecated')->default(0);

            $table->timestamp('email_verified_at')->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
