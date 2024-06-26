<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Organisation\Models\Organisation;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 255);
            $table->foreignIdFor(Organisation::class);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
