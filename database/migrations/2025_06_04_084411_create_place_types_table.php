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
        Schema::create('place_types', function (Blueprint $table) {
            $table->id();
            $table->string('resource')->comment('來源的 Type');
            $table->string('label')->comment('名稱');
            $table->string('key')->comment('Type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('place_types');
    }
};
