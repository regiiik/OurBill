<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bill_items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade')->onUpdate('cascade');  
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->decimal('qty', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
