<?php

namespace App\Repositories\Contracts;

use App\Values\Auth\UserData;
use App\Entities\User;

interface UserRepository
{
    public function findByUserData(UserData $userData): ?User;

    public function store(User $user): User;
}
