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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('brand');
            $table->string('model');
            $table->string('version');
            $table->string('model_year');
            $table->string('build_year');
            $table->integer('doors');
            $table->string('board');
            $table->string('chassi')->nullable();
            $table->string('transmission');
            $table->string('km');
            $table->text('description')->nullable();
            $table->string('sold');
            $table->string('category');
            $table->string('url_car');
            $table->string('old_price')->nullable();
            $table->string('price');
            $table->string('color');
            $table->string('fuel');
            $table->unsignedBigInteger('external_id')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
