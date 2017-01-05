<?php 

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateMenusClass extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('title', 100);
            $table->string('permalink', 255);
            $table->string('external_link', 255)->nullable();
            $table->string('template', 100);
            $table->char('is_active', 1)->default(0)->nullable();
            $table->char('is_system', 1)->default(0)->nullable();
            $table->unsignedInteger('sequence');
            $table->datetime('create_on');
            $table->datetime('last_update')->nullable();

            $table->unique('permalink', 'permalink');
        });

        Schema::create('menus_i18n', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedInteger('menu_id');
            $table->string('title', 100);
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
        Schema::drop('menus');

        Schema::drop('menus_i18n');
    }
}