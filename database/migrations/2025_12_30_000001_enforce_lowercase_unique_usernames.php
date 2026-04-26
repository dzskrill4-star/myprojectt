<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('users', 'username')) {
            return;
        }

        // Normalize existing data
        DB::table('users')->whereNotNull('username')->update([
            'username' => DB::raw('LOWER(username)')
        ]);

        // Fail fast if duplicates exist after normalization
        $duplicates = DB::table('users')
            ->select(DB::raw('LOWER(username) as username_lower'), DB::raw('COUNT(*) as total'))
            ->whereNotNull('username')
            ->groupBy(DB::raw('LOWER(username)'))
            ->having('total', '>', 1)
            ->limit(20)
            ->get();

        if ($duplicates->isNotEmpty()) {
            $list = $duplicates->map(fn($row) => $row->username_lower . ' (' . $row->total . ')')->implode(', ');
            throw new RuntimeException('Duplicate usernames found after lowercasing. Resolve these before continuing: ' . $list);
        }

        // Enforce uniqueness at DB level (best-effort if already exists)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('username', 'users_username_unique');
            });
        } catch (Throwable $e) {
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'already exists') || str_contains($msg, 'duplicate') || str_contains($msg, 'exists')) {
                return;
            }
            throw $e;
        }
    }

    public function down(): void {
        if (!Schema::hasColumn('users', 'username')) {
            return;
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_username_unique');
            });
        } catch (Throwable $e) {
            // ignore
        }
    }
};
