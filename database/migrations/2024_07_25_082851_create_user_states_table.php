<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserStatesTable extends Migration
{
    public function up()
    {
        Schema::create('user_states', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique();
            $table->string('state')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_states');
    }
}
