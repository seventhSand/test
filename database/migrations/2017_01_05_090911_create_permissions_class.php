<?php 

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreatePermissionsClass extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function(Blueprint $table)
        {
            $table->unsignedInteger('role_id')->nullable();
            $table->string('module', 25);
            $table->string('panel', 25);
            $table->string('permission', 25);
            $table->datetime('create_on');

            $table->unique(['role_id', 'module', 'panel', 'permission'], 'role_id-module-panel-permission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permissions');
    }
}