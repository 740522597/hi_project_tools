<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriortyFieldIntoPlanTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hp_plans', function (Blueprint $table) {
            $table->integer('urgency_level')->after('title')->default(1);
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
            $table->dropColumn('urgency_level');
        });
    }
}
