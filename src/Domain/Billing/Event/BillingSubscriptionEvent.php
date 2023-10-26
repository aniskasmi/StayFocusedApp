<?php

namespace App\Domain\Billing\Event;

use App\Domain\Auth\User;

class BillingSubscriptionEvent
{
    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
