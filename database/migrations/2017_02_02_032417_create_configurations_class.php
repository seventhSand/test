<?php 

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateConfigurationsClass extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('key', 255);
            $table->string('setting', 2000);
            $table->datetime('create_on');
            $table->datetime('last_update')->nullable();

            $table->unique('key', 'key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('configurations');
    }
}