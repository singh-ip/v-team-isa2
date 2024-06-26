<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Team\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_team', function (Blueprint $table) {
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Team::class);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_team');
    }
};
