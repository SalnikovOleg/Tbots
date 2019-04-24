<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Tcommands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcommands', function (Blueprint $table) {
            $table->bigIncrements('id');
	    $table->bigInteger('message_id');
	    $table->bigInteger('chat_id');
	    $table->bigInteger('command_id')->nullable();
	    $table->string('text');
	    $table->string('command')->nullable();
	    $table->string('args')->nullable();
	    $table->boolean('completed')->default(false);
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
        Schema::dropIfExists('tcommands');
    }
}
