<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_area', function (Blueprint $table) {
            $table->increments('area_id');
            $table->integer('code')->default(0)->comment('编码');
            $table->integer('parent_code')->default(0)->index('parent_code')->comment('上级编码');
            $table->boolean('level')->default(0)->index('level')->comment('层级');
            $table->string('name', 250)->nullable()->default('')->index('name')->comment('名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_area');
    }
}
