<?php

namespace App\Support;

class P2PAccess
{
    public static function allows($user): bool
    {
        if (!$user) {
            return false;
        }

        return ((int) $user->id === 24) || ($user->username === 'Amine13');
    }
}
