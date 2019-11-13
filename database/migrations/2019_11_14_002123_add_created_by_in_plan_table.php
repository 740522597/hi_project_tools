<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByInPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hp_plans', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
        });
        Schema::table('hp_tasks', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
        });
        Schema::table('task_comments', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
        });
        Schema::table('archived_tasks', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
        });
        Schema::table('archived_plans', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hp_plans', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
        Schema::table('hp_tasks', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
        Schema::table('task_comments', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
        Schema::table('archived_tasks', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
        Schema::table('archived_plans', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
}
