<?php

use Illuminate\Database\Migrations\Migration;

class CreateCostcentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('costcenters', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('costcenter_number')->nullable();
            $table->integer('manufacturer_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('depreciation_id')->nullable();
            $table->integer('eol')->nullable()->default(NULL);
            $table->string('image')->nullable();
            $table->text('notes')->nullable()->default(NULL);
            $table->softDeletes();
            $table->text('note')->nullable()->default(NULL);
            $table->integer('depreciation_id')->nullable()->default(null)->change();
            $table->timestamps();
            $table->tinyInteger('requestable')->default(0);
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('costcenters');
    }

}
