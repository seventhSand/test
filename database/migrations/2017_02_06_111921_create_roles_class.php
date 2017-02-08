<?php 

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateRolesClass extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedSmallInteger('role_level');
            $table->string('title', 25);
            $table->char('is_admin', 1)->default(0)->nullable();
            $table->char('is_system', 1)->default(0)->nullable();
            $table->char('is_active', 1)->default(1)->nullable();
            $table->datetime('create_on');
            $table->datetime('last_update')->nullable();

            $table->unique('title', 'title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('roles');
    }
}