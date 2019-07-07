<?php

namespace App\Values\Auth;

use Illuminate\Support\Facades\Hash;

class UserData
{
    public $name;
    public $email;
    public $password;

    public function __construct(string $email, string $password, string $name = '')
    {
        if (empty($email)) {
            throw new \LogicException('email is required');
        }

        if (empty($password)) {
            throw new \LogicException('password is required');
        }

        $this->name = $name;
        $this->email = $email;
        $this->password = Hash::make($password);
    }
}
