<?php

namespace pedroac\nonce;

use PHPUnit\Framework\TestCase;
use pedroac\nonce\Nonce;

/**
 * Nonce class unit tests.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class NonceTest extends TestCase
{
    /**
     * @covers \pedroac\nonce\Nonce::getName
     * @covers \pedroac\nonce\Nonce::__construct
     */
    public function testGetName()
    {
        $this->assertEquals(
            'my-form',
            (
                new Nonce(
                    'my-form',
                    'qwerty123',
                    new \DateTimeImmutable
                )
            )->getName()
        );
    }

    /**
     * @covers pedroac\nonce\Nonce::getValue
     * @covers pedroac\nonce\Nonce::__construct
     */
    public function testGetValue()
    {
        $this->assertEquals(
            'qwerty123',
            (
                new Nonce(
                    'my-form',
                    'qwerty123',
                    new \DateTimeImmutable
                )
            )->getValue()
        );
    }

    /**
     * @covers pedroac\nonce\Nonce::getExpiration
     * @covers pedroac\nonce\Nonce::__construct
     */
    public function testGetExpiration()
    {
        $now = new \DateTimeImmutable;
        $this->assertEquals(
            $now,
            (
                new Nonce('my-form', 'qwerty123', $now)
            )->getExpiration()
        );
    }

    /**
     * @covers pedroac\nonce\Nonce::isExpired
     * @covers pedroac\nonce\Nonce::__construct
     */
    public function testIsExpired()
    {
        $nonce = new Nonce(
            'my-form',
            'qwerty123',
            new \DateTimeImmutable('2018-03-29 12:30')
        );
        $this->assertTrue(
            $nonce->isExpired(
                new \DateTimeImmutable('2018-03-29 12:31')
            )
        );
        $this->assertFalse(
            $nonce->isExpired(
                new \DateTimeImmutable('2018-03-29 12:29')
            )
        );
        $this->assertFalse(
            $nonce->isExpired(
                new \DateTimeImmutable('2018-03-29 12:30')
            )
        );
    }

    /**
     * @covers pedroac\nonce\Nonce::isExpired
     * @covers pedroac\nonce\Nonce::__construct
     */
    public function testIsExpiredNow()
    {
        $this->assertTrue(
            (
                new Nonce(
                    'my-form',
                    'qwerty123',
                    (new \DateTimeImmutable)
                        ->sub(new \DateInterval('PT1H'))
                )
            )->isExpired()
        );
        $this->assertFalse(
            (
                new Nonce(
                    'my-form',
                    'qwerty123',
                    (new \DateTimeImmutable)
                        ->add(new \DateInterval('PT1H'))
                )
            )->isExpired()
        );
    }

    /**
     * @covers pedroac\nonce\Nonce::verify
     * @covers pedroac\nonce\Nonce::__construct
     */
    public function testVerify()
    {
        $nonce = new Nonce(
            'my-form',
            'qwerty123',
            new \DateTimeImmutable('2018-03-29 12:30')
        );
        $this->assertTrue(
            $nonce->verify(
                'my-form',
                'qwerty123',
                new \DateTimeImmutable('2018-03-29 12:29')
            )
        );
        $this->assertFalse(
            $nonce->verify(
                'my-form',
                'qwerty123',
                new \DateTimeImmutable('2018-03-29 12:31')
            )
        );
        $this->assertFalse(
            $nonce->verify(
                'my-form',
                'qwerty1234',
                new \DateTimeImmutable('2018-03-29 12:29')
            )
        );
        $this->assertFalse(
            $nonce->verify(
                'my-forms',
                'qwerty123',
                new \DateTimeImmutable('2018-03-29 12:29')
            )
        );
    }

    /**
     * @covers pedroac\nonce\Nonce::verify
     * @covers pedroac\nonce\Nonce::__construct
     */
    public function testVerifyNow()
    {
        $this->assertTrue(
            (
                new Nonce(
                    'my-form',
                    'qwerty123',
                    (new \DateTimeImmutable)
                        ->add(new \DateInterval('PT1H'))
                )
            )->verify(
                'my-form',
                'qwerty123'
            )
        );
        $this->assertFalse(
            (
                new Nonce(
                    'my-form',
                    'qwerty123',
                    (new \DateTimeImmutable)
                        ->sub(new \DateInterval('PT1H'))
                )
            )->verify(
                'my-form',
                'qwerty123'
            )
        );
    }
}