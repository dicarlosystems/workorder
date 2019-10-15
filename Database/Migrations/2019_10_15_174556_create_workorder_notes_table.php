<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkOrderNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(strtolower('workorder_notes'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('workorder_id')->index();
            $table->string('note');

            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_deleted')->default(false);

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');
            
            $table->unsignedInteger('public_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(strtolower('workorder_notes'));
    }
}
