<?php

namespace App\Domain\Billing\Exception;

class BillingNotBanException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Impossible de bannir un utilisateur ayant un abonnement', 0, null);
    }
}
