<?php

namespace App\Infrastructure\Payment;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Ajoute des méthodes permettant de récupérer des infos publics pour les paiements.
 */
class PaymentTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly string $stripePublicKey = ''
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('stripeKey', [$this, 'getStripePublicKey'])
        ];
    }

    public function getStripePublicKey(): string
    {
        return $this->stripePublicKey;
    }
}
