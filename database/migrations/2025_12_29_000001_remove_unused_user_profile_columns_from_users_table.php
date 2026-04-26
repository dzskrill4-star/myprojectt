<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['country', 'mobile', 'state', 'city'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country', 191)->nullable();
            }

            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile', 191)->nullable();
            }

            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state', 191)->nullable();
            }

            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 191)->nullable();
            }
        });
    }
};
