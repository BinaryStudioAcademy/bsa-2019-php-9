<?php

namespace App\Services\Contracts;

use App\Values\Auth\UserData;
use App\Entities\User;

interface AuthService
{
    public function register(UserData $userData): User;

    public function auth(string $token): User;
}
