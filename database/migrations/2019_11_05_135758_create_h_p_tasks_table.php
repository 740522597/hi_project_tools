<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHPTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hp_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('plan_id');
            $table->string('title');
            $table->string('description');
            $table->string('prefix');
            $table->integer('code');
            $table->integer('assign_to')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->integer('estimation')->nullable();
            $table->string('status')->default('PENDING');
            $table->integer('urgency_level')->default(1);
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
        Schema::dropIfExists('hp_tasks');
    }
}
