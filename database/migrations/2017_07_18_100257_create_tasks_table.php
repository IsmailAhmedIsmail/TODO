<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->text('title');
            $table->text('description');
            $table->boolean('completed')->default(false);
            $table->boolean('private')->default(false);
            $table->dateTime('deadline');
            $table->timestamps();
        });
        Schema::create('task_user_follow', function (Blueprint $table) {
            $table->integer('task_id');
            $table->integer('user_id');
            $table->primary(['task_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_user_follow');
    }
}
