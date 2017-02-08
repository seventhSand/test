<?php 

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateHistoriesClass extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('parent_id');
            $table->unsignedSmallInteger('role_level');
            $table->string('action', 25);
            $table->string('actor', 25);
            $table->text('properties');
            $table->datetime('create_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('histories');
    }
}