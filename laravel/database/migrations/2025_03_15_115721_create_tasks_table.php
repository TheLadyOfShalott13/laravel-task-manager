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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');                                // Task title
            $table->text('description')->nullable();                // Task description
            $table->date('due_date')->nullable();                   // Due date
            $table->timestamp('completed')->nullable();             // Completion status boolean flag
            $table->timestamps();                                          // Timestamps for creation and updation
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
