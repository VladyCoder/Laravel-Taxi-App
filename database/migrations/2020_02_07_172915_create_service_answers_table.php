<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id');
            $table->string('answer');
            $table->integer('fixed')->nullable();
            $table->integer('price')->nullable();
            $table->integer('minute')->nullable();
            $table->integer('hour')->nullable();
            $table->integer('distance')->nullable();
            $table->enum('calculator', ['MIN', 'HOUR', 'DISTANCE', 'DISTANCEMIN', 'DISTANCEHOUR'])->default('MIN');
            $table->string('description')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('service_answers');
    }
}