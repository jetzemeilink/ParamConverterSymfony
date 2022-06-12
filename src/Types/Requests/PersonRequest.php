<?php

namespace App\Types\Requests;

class PersonRequest
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $age = null;
    public ?string $city = null;
    public ?bool $isMarried = null;
}