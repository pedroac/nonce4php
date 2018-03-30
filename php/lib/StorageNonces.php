<?php

namespace pedroac\nonce;

/**
 * Interface for nonces storages.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
interface StorageNonces
{
    /**
     * Store a nonce.
     *
     * @param Nonce $nonce The nonce that should be stored.
     * @return void
     */
    public function store(Nonce $nonce);

    /**
     * Retrieve a stored nonce by its name.
     *
     * @param string $name The name of the nonce that should be retrieved.
     * @return Nonce|null The retrieved nonce or null.
     */
    public function get(string $name): ?Nonce;

    /**
     * Remove a nounce from the storage.
     *
     * @param string $nonceReference The nonce name that should be removed.
     * @return void
     */
    public function expire(string $name);

    /**
     * Remove all the expired nonces.
     *
     * @return \DateTimeInterface Date and time as the current date and time.
     */
    public function purge(\DateTimeInterface $now=null);
}