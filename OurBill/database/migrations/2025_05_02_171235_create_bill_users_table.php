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
        Schema::create('bill_users', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('bill_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade')->onUpdate('cascade'); 
            $table->decimal('user_subtotal', 12, 2);
            $table->decimal('treat_deduction', 12, 2)->default(0);
            $table->decimal('treat_credit', 12, 2)->default(0);
            $table->decimal('final_total', 12, 2);
            $table->boolean('is_treater')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_users');
    }
};
