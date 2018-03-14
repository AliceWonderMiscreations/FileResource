<?php
declare(strict_types=1);

/**
 * This class is only meant for testing that the phpunit install actually works.
 * If any of these tests fail, there is likely issue with how phpunit is installed.
 *
 * @package AWonderPHP/FileResource
 * @author  Sebastian Bergmann <sebastian@phpunit.de>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link    https://phpunit.de/getting-started/phpunit-7.html
 */

use PHPUnit\Framework\TestCase;

/**
 * This class is only meant for testing that the phpunit install actually works.
 */
final class EmailTest extends TestCase
{
    /**
     * First example test case
     *
     * @return void
     */
    public function testCanBeCreatedFromValidEmailAddress(): void
    {
        $this->assertInstanceOf(
            \AWonderPHP\FileResource\Email::class,
            \AWonderPHP\FileResource\Email::fromString('user@example.com')
        );
    }

    /**
     * Second example test case
     *
     * @return void
     */
    public function testCannotBeCreatedFromInvalidEmailAddress(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        \AWonderPHP\FileResource\Email::fromString('invalid');
    }

    /**
     * Third example test case
     *
     * @return void
     */
    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(
            'user@example.com',
            \AWonderPHP\FileResource\Email::fromString('user@example.com')
        );
    }
// end of class
}
?>