<?php

namespace pedroac\nonce\Random;
use \pedroac\nonce\Random;

/**
 * Cryptographically secure pseudo-random hexadecimal numbers generator.
 * @link http://php.net/manual/en/intro.csprng.php
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class HexRandomizer implements Random
{
    /**
     * The length of the random strings in digits number.
     *
     * @var int
     */
    private $length;

    /**
     * Create an hexadecimal number randomizer.
     *
     * @throws \LengthException
     * @param integer $length The length of the random strings in digits number.
     */
    public function __construct(int $length)
    {
        if ($length < 1) {
            throw new \LengthException(
                "Cannot set length as $length, which must be equal or greater than 1."
            );
        }
        $this->length = $length;
    }

    /**
     * Generate a random hexadecimal number representation.
     *
     * @return string The random string.
     */
    public function randomize(): string
    {
        if ($this->length % 2 == 0) {
            return bin2hex(
                random_bytes($this->length/2)
            );
        }
        return substr(
            bin2hex(
                random_bytes($this->length/2+1)
            ),
            1
        );
    }
}