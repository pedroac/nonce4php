<?php
/**
 * Nonce class.
 */

namespace pedroac\nonce;
use Kdyby\DateTimeProvider\DateTimeProviderInterface;

/**
 * A nonce value object.
 * 
 * It should be immutable.
 * It neither generates random nor stores nonces.
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
     * DateTime provider that should be used as the current date and time.
     *
     * @var DateTimeProviderInterface
     */
    private $now;

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
     * @param DateTimeProviderInterface $now DateTime provider that should 
     *  be used as the current date and time.
     */
    public function __construct(
        string $name,
        string $value,
        \DateTimeInterface $expiration,
        DateTimeProviderInterface $now = null
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->expiration = $expiration;
        $this->now = $now ?? 
            new \Kdyby\DateTimeProvider\Provider\CurrentProvider;
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
     * @return string The value.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Check if the nonce has expired.
     * It has not expired if the expiration date and time and current
     * date and time are the same. 
     *
     * @return boolean Is the nonce expired?
     */
    public function isExpired(): bool
    {
        return $this->now->getDateTime() > $this->expiration;
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