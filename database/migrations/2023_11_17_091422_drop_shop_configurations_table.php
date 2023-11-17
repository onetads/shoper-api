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
        Schema::dropIfExists('shop_configurations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('shop_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')
                ->references('id')
                ->on('shops')
                ->onDelete('cascade');
            $table->string('website_id', 128);
            $table->boolean('substitute_product')->default(0);
            $table->timestamps();
        });
    }
};
