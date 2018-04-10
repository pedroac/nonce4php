<?php

namespace pedroac\nonce;

/**
 * Interface for string randomizers.
 * 
 * It might be used to create nonces with random values.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
interface Random
{
    /**
     * Generate a string with random characters.
     * The returned string might be used as a nonce value object.
     *
     * @return string The generated string with random characters.
     */
    public function randomize(): string;
}