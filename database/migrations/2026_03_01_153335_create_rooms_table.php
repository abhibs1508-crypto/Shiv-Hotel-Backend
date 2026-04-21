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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('type');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            
            // The Advanced Columns
            $table->integer('capacity')->default(2);
            $table->json('specs')->nullable();
            $table->json('amenities')->nullable();
            
            $table->string('image')->nullable();
            $table->string('status')->default('available');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
