<?php

namespace pedroac\nonce\StorageNonces;
use pedroac\nonce\Nonce;

/**
 * Nonces array storage.
 * It might be used to change the PHP session state.
 * The injected array should be modified when a nonce is stored or expired.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class NoncesArrayStorage implements \pedroac\nonce\StorageNonces
{
    private $data;

    /**
     * Create a nonces array storage instance.
     * 
     * @param array $data The array that should be used to store nonces.
     */
    public function __construct(array &$data) {
        $this->data = &$data;
    }

    /**
     * @see \pedroac\nonce\StorageNonces::store
     *
     * @param Nonce $nonce
     * @return void
     */
    public function store(Nonce $nonce)
    {
        $this->data[$nonce->getName()] = $nonce;
    }

    /**
     * @see \pedroac\nonce\StorageNonces::get
     *
     * @param string $name
     * @return Nonce|null
     */
    public function get(string $name): ?Nonce
    {
        if (!isset( $this->data[$name])) {
            return null;
        }
        return  $this->data[$name];
    }

    /**
     * @see \pedroac\nonce\StorageNonces::expire
     *
     * @param string $name
     * @return void
     */
    public function expire(string $name)
    {
        unset( $this->data[$name]);
    }

    /**
     * @see \pedroac\nonce\StorageNonces::purge
     *
     * @return \DateTimeInterface
     */
    public function purge(\DateTimeInterface $now=null)
    {
        foreach ( $this->data as $name => $nonce) {
            if ($nonce->isExpired($now)) {
                unset($this->data[$name]);
            }
        }
    }
}