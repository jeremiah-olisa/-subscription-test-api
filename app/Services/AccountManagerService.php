<?php

namespace App\Service;

use App\Models\User;

class AccountManagerService
{
    protected User $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    // Your methods for repository
    public function _function()
    {
    }
}