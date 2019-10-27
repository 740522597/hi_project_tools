<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatUserMsgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_user_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('open_id');
            $table->string('from_user');
            $table->string('to_user');
            $table->dateTime('create_time');
            $table->text('content');
            $table->string('msg_type');
            $table->string('event')->nullable();
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
        Schema::dropIfExists('wechat_user_messages');
    }
}
