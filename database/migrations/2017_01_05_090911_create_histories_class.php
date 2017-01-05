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
            $table->unsignedSmallInteger('role_level');
            $table->string('action', 25);
            $table->string('actor', 25);
            $table->string('table_name', 100);
            $table->unsignedBigInteger('table_id');
            $table->longText('properties');
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