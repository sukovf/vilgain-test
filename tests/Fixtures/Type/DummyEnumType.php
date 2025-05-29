<?php

namespace App\Tests\Fixtures\Type;

use App\Type\EnumType;

class DummyEnumType extends EnumType
{
    protected function getEnumClass(): string
    {
        return DummyBackedEnum::class;
    }
}