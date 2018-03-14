<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testCanBeCreatedFromValidEmailAddress(): void
    {
        $this->assertInstanceOf(
            \AWonderPHP\FileResource\Email::class,
            \AWonderPHP\FileResource\Email::fromString('user@example.com')
        );
    }

    public function testCannotBeCreatedFromInvalidEmailAddress(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        \AWonderPHP\FileResource\Email::fromString('invalid');
    }

    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(
            'user@example.com',
            \AWonderPHP\FileResource\Email::fromString('user@example.com')
        );
    }
}
