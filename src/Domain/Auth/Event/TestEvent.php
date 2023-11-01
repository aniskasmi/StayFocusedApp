<?php

namespace App\Domain\Auth\Event;

class TestEvent
{
    public function __construct(private readonly string $string)
    {
    }

    public function getString()
    {
        return $this->string;
    }
}
