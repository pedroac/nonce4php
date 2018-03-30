<?php

namespace pedroac\nonce;
use pedroac\nonce\Random;
use pedroac\nonce\StorageNonces;

/**
 * Nonces manager.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class NoncesManager
{
    /**
     * The random nonce value generator.
     *
     * @var Random
     */
    private $random;
    /**
     * The nonces storage.
     *
     * @var StorageNonces
     */
    private $storage;
    /**
     * The nonces expiration interval.
     *
     * @var \DateInterval
     */
    private $expirationInterval;
    /**
     * Date and time that should be used as the current date and time.
     *
     * @var \DateInterval
     */
    private $now;

    /**
     * Create a nonces manager.
     * 
     * The default nonces expiration interval is 1 hour.
     * If `$now` is not NULL, it should used to override the system
     * clock and used as the current date and time. That's useful for
     * Unit Tests.
     *
     * @param StorageNounces $storage The nonces storage.
     * @param Random $random The random nonces values generator.
     * @param \DateInterval $expirationInterval The nonces expiration interval.
     * @param \DateTimeInterface $now Date and time that should be used as current date and time.
     */
    public function __construct(
        StorageNonces $storage,
        Random $random,
        \DateInterval $expirationInterval=null,
        \DateTimeInterface $now=null
    ) {
        if (!$expirationInterval) {
            $expirationInterval = new \DateInterval('PT1H');
        }
        $this->now = $now;
        $this->random = $random;
        $this->storage = $storage;
        $this->expirationInterval = $expirationInterval;
    }

    /**
     * Create a nonce.
     * 
     * The nonce should be randomly generated and temporarily stored.
     * If `$expiration` is not NULL, the manager expiration interval should
     * be ignored.
     *
     * @param string $name The name that can be used to identify the nonce.
     * @param \DateTimeInterface $expiration The nonce expiration date and time.
     * @return Nonce The created nonce.
     */
    public function create(
        string $name,
        \DateTimeInterface $expiration=null
    ): Nonce {
        if (!$expiration) {
            $expiration = 
                ($this->now ?? new \DateTime)
                    ->add($this->expirationInterval);
        }
        $this->storage->store(
            $nonce = new Nonce(
                $name,
                $this->random->randomize(),
                $expiration
            )
        );
        return $nonce;
    }

    /**
     * Check if there's a nounce with the specified name and value 
     * and which has not expired.
     * 
     * @see \pedroac\nonce\Nonce::verify
     *
     * @param string $name
     * @param string $value
     * @return boolean Is there a nonce 
     */
    public function verify(
        string $name,
        string $value
    ): bool {
        $nonce = $this->storage->get($name);
        if (!$nonce) {
            return false;
        }
        return $nonce->verify($name, $value, $this->now);
    }

    /**
     * Remove a specified nonce.
     * 
     * @see \pedroac\nonce\StorageNonces::expire
     *
     * @param Nonce|string $name The nonce name that should be removed.
     * @return void
     */
    public function expire(string $name)
    {
        $this->storage->expire($name);
    }

    /**
     * Remove all expired nonces from the storage.
     * 
     * @see \pedroac\nonce\StorageNonces::purge
     *
     * @return void
     */
    public function purge()
    {
        $this->storage->purge($this->now);
    }
}