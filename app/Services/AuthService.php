<?php

namespace App\Services;

use App\Services\Contracts\AuthService as IAuthService;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Values\Auth\UserData;
use App\Values\Auth\Token;
use App\Entities\User;

class AuthService implements IAuthService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function auth(string $token): User
    {
        $userData = Token::parse($token);

        $user = $this->userRepository->findByEmail($userData->email);

        if ($user === null) {
            throw new \LogicException('User does not exist');
        }

        if ($userData->checkPassword($user->password) === false) {
            throw new \LogicException('Password is incorrect');
        }

        Auth::login($user);

        return $user;
    }

    public function register(UserData $userData): User
    {
        $user = new User;

        $user->name = $userData->name;
        $user->email = $userData->email;
        $user->password = $userData->password;

        $user = $this->userRepository->store($user);

        return $user;
    }
}

