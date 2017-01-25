<?php 

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateSamplesClass extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('samples', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title', 100);
            $table->string('file', 100);
            $table->text('description')->nullable();
            $table->unsignedInteger('sequence');
            $table->datetime('create_on');
            $table->datetime('last_update')->nullable();
        });

        Schema::create('samples_i18n', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->char('lang_code', 2);
            $table->unsignedInteger('sample_id');
            $table->string('title', 100);
            $table->string('file', 100);
            $table->text('description')->nullable();
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
        Schema::drop('samples');

        Schema::drop('samples_i18n');
    }
}