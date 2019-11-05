<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHPProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hp_projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('prefix');
            $table->timestamps();
        });
        \App\Models\HPProject::query()
            ->firstOrCreate([
               'name' => 'RIOT',
               'description' => 'RIOT Merch/OMS',
               'prefix' => 'RIOT'
            ]);
        \App\Models\HPProject::query()
            ->firstOrCreate([
                'name' => 'CETTIRE',
                'description' => 'CETTIRE PMS',
                'prefix' => 'CET'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hp_projects');
    }
}
