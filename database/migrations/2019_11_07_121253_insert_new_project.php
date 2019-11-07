<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertNewProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\HPProject::query()
            ->firstOrCreate([
                'name' => 'Hi-Project',
                'description' => '我的Hi-Project项目管理工具',
                'prefix' => 'HP'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\HPProject::query()
            ->where([
                'name' => 'Hi-Project',
            ])->delete();
    }
}
