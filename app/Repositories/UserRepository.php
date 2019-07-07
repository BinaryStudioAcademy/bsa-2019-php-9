<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserRepository as IUserRepository;
use App\Values\Auth\UserData;
use App\Entities\User;

class UserRepository implements IUserRepository
{
    public function findByUserData(UserData $userData): ?User
    {
        $query = User::where('email', $userData->email)
            ->where('password', $userData->password);

        if ($userData->name) {
            $query = $query->where('name', $userData->name);
        }

        return $query->first();
    }

    public function store(User $user): User
    {
        $user->save();

        return $user;
    }
}
