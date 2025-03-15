<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id')->default(1);                                  //Adding user ID to tasks table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');           // Add the foreign key relationship
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);                                                                                       // Drop the foreign key first
            $table->dropColumn('user_id');                                                                                  // Then drop the column
        });
    }
};
