<?php

namespace pedroac\nonce;
use PHPUnit\Framework\TestCase;
use pedroac\nonce\NoncesManagerTest;
use Kdyby\DateTimeProvider\Provider\MutableProvider;
use Symfony\Component\Cache\Simple\ArrayCache;

/**
 * NoncesManager class unit tests.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class NoncesManagerTest extends TestCase
{
    /**
     * @covers \pedroac\nonce\NoncesManager::create
     * @covers \pedroac\nonce\NoncesManager::__construct
     */
    public function testCreate()
    {
        $now = new MutableProvider(new \DateTimeImmutable);
        $manager = new NoncesManager(
            new ArrayCache(60),
            new FakeRandom,
            new \DateInterval('PT1S'),
            $now
        );
        $nonce = $manager->create('my-form');

        $this->assertEquals(
            'my-form',
            $nonce->getName()
        );

        $this->assertEquals(
            'qwerty123',
            $nonce->getValue()
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
     * @covers \pedroac\nonce\NoncesManager::create
     * @covers \pedroac\nonce\NoncesManager::__construct
     */
    public function testCreateWithDefault()
    {
        $now = new MutableProvider(new \DateTimeImmutable);
        $manager = new NoncesManager(
            new ArrayCache(60),
            null,
            null,
            $now
        );
        $nonce = $manager->create('my-form');

        $this->assertEquals(
            'my-form',
            $nonce->getName()
        );

        $nonce = $manager->create();

        $this->assertTrue(
            strlen($nonce->getName()) > 10
        );
        
        $this->assertTrue(
            strlen($nonce->getValue()) > 10
        );

        $this->assertFalse(
            $nonce->isExpired()
        );

        $now->changePrototype(
            (new \DateTimeImmutable)
                ->add(new \DateInterval('PT50M'))
        );
        $this->assertFalse(
            $nonce->isExpired()
        );

        $now->changePrototype(
            (new \DateTimeImmutable)
                ->add(new \DateInterval('PT1H'))
        );
        $this->assertTrue(
            $nonce->isExpired()
        );
    }

    /**
     * @covers \pedroac\nonce\NoncesManager::create
     * @covers \pedroac\nonce\NoncesManager::verify
     * @covers \pedroac\nonce\NoncesManager::__construct
     */
    public function testVerify()
    {
        $now = new MutableProvider(new \DateTimeImmutable);
        $manager = new NoncesManager(
            new ArrayCache(60),
            new FakeRandom,
            new \DateInterval('PT1S')
        );
        $manager->create('my-form-1');

        $this->assertTrue(
            $manager->verify('my-form-1', 'qwerty123')
        );

        $this->assertFalse(
            $manager->verify('my-form-1', 'qwerty456')
        );

        $this->assertFalse(
            $manager->verify('my-form12', 'qwerty123')
        );

        $now->changePrototype(
            (new \DateTimeImmutable)
                ->add(new \DateInterval('PT1S'))
        );
        $this->assertTrue(
            $manager->verify('my-form-1', 'qwerty123')
        );
    }

    /**
     * @covers \pedroac\nonce\NoncesManager::create
     * @covers \pedroac\nonce\NoncesManager::expire
     * @covers \pedroac\nonce\NoncesManager::__construct
     */
    public function testExpire()
    {
        $manager = new NoncesManager(
            $storage = new ArrayCache(60),
            new FakeRandom
        );
        $nonce = $manager->create('my-form');
        $this->assertEquals($nonce, $storage->get('my-form'));
        $manager->expire('my-form');
        $this->assertSame(null, $storage->get('my-form'));
    }

    /**
     * @covers \pedroac\nonce\NoncesManager::create
     * @covers \pedroac\nonce\NoncesManager::verify
     * @covers \pedroac\nonce\NoncesManager::__construct
     */
    public function testInvalidCachedItem()
    {
        $manager = new NoncesManager(
            $storage = new ArrayCache(60),
            new FakeRandom
        );
        $manager->create('my-form');
        $storage->set('my-form', 'not-nonce');
        $this->expectException(\RunTimeException::class);
        $manager->verify('my-form', 'qwerty123');
    }
}

class FakeRandom implements \pedroac\nonce\Random
{
    public function randomize(): string
    {
        return 'qwerty123';
    }
}