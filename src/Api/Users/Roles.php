<?php

namespace Supp\Api\Users;

enum Roles: int
{
    case Admin = 1;
    case SuperAdmin = 2;
    case Client = 3;

    function roleId() : int {
        return match ($this) {
            Roles::Admin => 1,
            Roles::SuperAdmin => 2,
            Roles::Client => 3
        };
    }
}