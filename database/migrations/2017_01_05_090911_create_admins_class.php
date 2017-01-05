<?php 

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateAdminsClass extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('username', 25);
            $table->string('password', 100);
            $table->string('email', 100);
            $table->char('is_system', 1)->default(0)->nullable();
            $table->char('is_active', 1)->default(1)->nullable();
            $table->datetime('create_on');
            $table->datetime('last_update')->nullable();

            $table->unique('username', 'username');
            $table->unique('email', 'email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('admins');
    }
}