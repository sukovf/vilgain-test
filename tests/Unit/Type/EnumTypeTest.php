<?php

namespace App\Tests\Unit\Type;

use App\Tests\Fixtures\Type\DummyBackedEnum;
use App\Tests\Fixtures\Type\DummyEnumType;
use App\Tests\Fixtures\Type\DummyPlainEnum;
use App\Type\Exception\InvalidArgumentException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EnumTypeTest extends TestCase
{
    private AbstractPlatform&MockObject $platformMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->platformMock = $this->createMock(AbstractPlatform::class);
    }

    public function testBasics(): void
    {
        $type = new DummyEnumType();

        $this->assertEquals(DummyBackedEnum::FOO->value, $type->convertToDatabaseValue(DummyBackedEnum::FOO, $this->platformMock));

        $this->assertEquals(DummyBackedEnum::FOO, $type->convertToPHPValue(DummyBackedEnum::FOO->value, $this->platformMock));
    }

    public function testInvalidEnum(): void
    {
        $type = new DummyEnumType();

        $this->expectException(InvalidArgumentException::class);
        $type->convertToDatabaseValue(DummyPlainEnum::FOO, $this->platformMock);

        $this->expectException(InvalidArgumentException::class);
        $type->convertToPHPValue([], $this->platformMock);

        $this->expectException(InvalidArgumentException::class);
        $type->convertToPHPValue('this is not in the enum', $this->platformMock);
    }
}