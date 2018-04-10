<?php

namespace pedroac\nonce;

use PHPUnit\Framework\TestCase;
use pedroac\nonce\Nonce;
use Kdyby\DateTimeProvider\Provider\MutableProvider;

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
     * @covers pedroac\nonce\Nonce::isExpired
     * @covers pedroac\nonce\Nonce::__construct
     */
    public function testIsExpired()
    {
        $now = new MutableProvider(new \DateTimeImmutable);
        $nonce = new Nonce(
            'my-form',
            'qwerty123',
            (new \DateTimeImmutable)->add(new \DateInterval('PT1S')),
            $now
        );

        $this->assertFalse(
            $nonce->isExpired()
        );

        $now->changePrototype(
            (new \DateTimeImmutable)
                ->add(new \DateInterval('PT1S'))
        );
        $this->assertTrue(
            $nonce->isExpired()
        );
    }

    /**
     * @covers pedroac\nonce\Nonce::verify
     * @covers pedroac\nonce\Nonce::__construct
     */
    public function testVerify()
    {
        $now = new MutableProvider(new \DateTimeImmutable);
        $nonce = new Nonce(
            'my-form',
            'qwerty123',
            (new \DateTimeImmutable)->add(new \DateInterval('PT1S'))
        );
        
        $this->assertTrue(
            $nonce->verify(
                'my-form',
                'qwerty123'
            )
        );
        
        $this->assertFalse(
            $nonce->verify(
                'my-form',
                'qwerty123a'
            )
        );

        $this->assertFalse(
            $nonce->verify(
                'my-forma',
                'qwerty123'
            )
        );
        
        $now->changePrototype(
            (new \DateTimeImmutable)
                ->add(new \DateInterval('PT1S'))
        );
        $this->assertFalse(
            $nonce->verify(
                'my-form',
                'qwerty1234'
            )
        );
    }
}