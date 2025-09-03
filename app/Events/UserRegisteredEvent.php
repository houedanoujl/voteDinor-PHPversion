<?php

namespace App\Events;

use App\Models\User;

class UserRegisteredEvent
{
    public function __construct(
        public User $user,
        public string $method = 'standard',
        public array $context = []
    ) {
    }
}


