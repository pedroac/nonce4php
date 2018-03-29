<?php

namespace pedroac\nonce;

/**
 * A nonce value object.
 * It should be immutable.
 * It neither generates random nonces nor stores nonces.
 * 
 * @link https://en.wikipedia.org/wiki/Cryptographic_nonce 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class Nonce
{
    /**
     * The name that can be used to identify a nonce.
     *
     * @var string
     */
    private $name;
    /**
     * The value, supposedly random characters.
     *
     * @var string
     */
    private $value;
    /**
     * The expiration date and time.
     *
     * @var \DateTimeInterface
     */
    private $expiration;

    /**
     * Create a nonce value object.
     * 
     * The name can be used to identify a nonce, supposedly according to 
     * some context, for instance, a specific form submition.
     * The value is supposedly a random set of characters.
     * The expiration date and time is used to check the nonce validity.
     *
     * @param string $name The name used to identify the nonce.
     * @param string $value The value.
     * @param \DateTimeInterface $expiration The expiration date and time.
     */
    public function __construct(
        string $name,
        string $value,
        \DateTimeInterface $expiration
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->expiration = $expiration;
    }

    /**
     * Return the name that identifies the nonce.
     *
     * @return string The name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the value.
     *
     * @return string The 
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Return the expiration date and time.
     *
     * @return \DateTimeInterface
     */
    public function getExpiration(): \DateTimeInterface
    {
        return $this->expiration;
    }

    /**
     * Check if the nonce has expired.
     * It has not expired if the expiration date and time and current
     * date and time are the same. 
     *
     * @param \DateTimeInterface $now Date and time as the current date and time.
     * @return boolean Is the nonce expired?
     */
    public function isExpired(\DateTimeInterface $now=null): bool
    {
        if (!$now) {
            $now = new \DateTimeImmutable;
        }
        return $now > $this->expiration;
    }

    /**
     * Verify the nonce against a specified name and value.
     * 
     * Return TRUE if the nonce has a specified name and value and if
     * it's not expired (sucessful verification), otherwise it 
     * should return FALSE (failed verification).
     *
     * @param string $name The name that should be compared.
     * @param string $value The value that shoud be compared.
     * @param \DateTimeImmutable $now Date and time as the current date and time.
     * @return boolean Was the verification successful?
     */
    public function verify(
        string $name,
        string $value,
        \DateTimeImmutable $now=null
    ): bool {
        return $this->name == $name
               && $this->value == $value
               && !$this->isExpired($now);
    }
}