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
    if (!Schema::hasTable('badge_rewards')) {

        Schema::create('badge_rewards', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->integer('user_badge_id')->default(0);
            $table->string('trx')->nullable();
            $table->decimal('amount', 28, 8)->default(0);
            $table->string('currency')->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();
        });

    }
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_rewards');
    }
};
