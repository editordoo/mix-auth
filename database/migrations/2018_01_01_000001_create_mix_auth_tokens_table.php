<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMixAuthTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mix_auth_tokens', function (Blueprint $table) {
//            $table->increments('id');
            $table->integer('user_id');
            $table->enum('guard',array_keys(config('mix-auth.guards')));
            $table->string('token',100);
            $table->string('prefix',config('mix-auth.prefix_length'));
            $table->dateTime('last_request')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->unique(['user_id','guard', 'prefix', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mix_auth_tokens');
    }
}
