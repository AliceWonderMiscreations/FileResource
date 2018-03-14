<?php
declare(strict_types=1);

/**
 * This class is only meant for testing that the phpunit install actually works
 *
 * @package AWonderPHP/FileResource
 * @author  Sebastian Bergmann <sebastian@phpunit.de>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link    https://phpunit.de/getting-started/phpunit-7.html
 */

namespace AWonderPHP\FileResource;

/**
 * A class for testing phpunit install
 */
final class Email
{
    /**
     * @var string
     */
    private $email;

    /**
     * The constructor function
     *
     * @param string $email The email address to test
     */
    private function __construct(string $email)
    {
        $this->ensureIsValidEmail($email);

        $this->email = $email;
    }

    /**
     * A test function
     *
     * @param string $email An email address to test
     *
     * @return self
     */
    public static function fromString(string $email): self
    {
        return new self($email);
    }

    /**
     * A test function
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * A test function
     *
     * @param string $email An e-mail address to test
     *
     * @return void
     */
    private function ensureIsValidEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '"%s" is not a valid email address',
                    $email
                )
            );
        }
    }
}
