<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Test middleware - shows if role check is DB-backed or session-cached
Route::middleware(['web'])->group(function () {
    Route::get('/test/auth-check', function () {
        DB::enableQueryLog();
        
        $isLoggedIn = Auth::guard('admin')->check();
        
        $queries = DB::getQueryLog();
        
        return response()->json([
            'logged_in' => $isLoggedIn,
            'query_count' => count($queries),
            'queries' => $queries,
            'current_user' => Auth::guard('admin')->user() ? Auth::guard('admin')->user()->id : null,
            'session_lifetime' => config('session.lifetime'),
            'session_driver' => config('session.driver'),
            'note' => 'If query_count > 0: Auth checks database on each request (DB source of truth)'
        ]);
    });

    Route::get('/test/multiple-checks', function () {
        DB::enableQueryLog();
        
        // First check
        Auth::guard('admin')->check();
        $firstCheck = count(DB::getQueryLog());
        
        // Second check  
        Auth::guard('admin')->check();
        $secondCheck = count(DB::getQueryLog()) - $firstCheck;
        
        // Third check
        Auth::guard('admin')->check();
        $thirdCheck = count(DB::getQueryLog()) - $firstCheck - $secondCheck;
        
        return response()->json([
            'first_check_queries' => $firstCheck,
            'second_check_queries' => $secondCheck,
            'third_check_queries' => $thirdCheck,
            'total_queries' => count(DB::getQueryLog()),
            'all_queries' => DB::getQueryLog(),
            'analysis' => $secondCheck === 0 && $thirdCheck === 0 
                ? 'Auth is cached - not DB backed on each call'
                : 'Auth queries DB on each check'
        ]);
    });
});
