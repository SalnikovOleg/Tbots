<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_tasks', function (Blueprint $table) {
    	    $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->integer('number');
            $table->string('pair');
            $table->decimal('min',10,2)->nullable();
            $table->decimal('max',10,2)->nullable();
            $table->boolean('active')->default(false);
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
        Schema::dropIfExists('ex_tasks');
    }
}
