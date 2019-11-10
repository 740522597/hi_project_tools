<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivedTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archived_tasks', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('plan_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('prefix')->nullable();
            $table->integer('code')->nullable();
            $table->integer('assign_to')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->integer('estimation')->nullable();
            $table->string('status');
            $table->integer('urgency_level')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('notified_at')->nullable();
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
        Schema::dropIfExists('archived_tasks');
    }
}
