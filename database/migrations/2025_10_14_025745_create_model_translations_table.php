<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_translations', function (Blueprint $table) {
            $table->string('namespace');
            $table->string('group');
            $table->string('item');
            $table->string('locale');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->primary(['namespace', 'group', 'item', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_translations');
    }
};
