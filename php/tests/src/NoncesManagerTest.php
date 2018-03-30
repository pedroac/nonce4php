<?php

namespace pedroac\nonce;

use PHPUnit\Framework\TestCase;
use pedroac\nonce\NoncesManagerTest;

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
        $manager = new NoncesManager(
            $storage = new FakeNoncesStorage,
            new FakeRandom,
            new \DateInterval('PT30M'),
            new \DateTimeImmutable('2018-03-30 12:00')
        );
        $nonce = $manager->create('my-form');
        $this->assertEquals('my-form', $nonce->getName());
        $this->assertEquals('qwerty123', $nonce->getValue());
        $this->assertEquals(
            (new \DateTimeImmutable('2018-03-30 12:30'))
                ->getTimestamp(),
            $nonce->getExpiration()->getTimestamp()
        );
        $this->assertSame($nonce, $storage->get('my-form'));
    }

    /**
     * @covers \pedroac\nonce\NoncesManager::create
     * @covers \pedroac\nonce\NoncesManager::__construct
     */
    public function testCreateWithDefaultInterval()
    {
        $manager = new NoncesManager(
            new FakeNoncesStorage,
            new FakeRandom,
            null,
            new \DateTimeImmutable('2018-03-30 12:00')
        );
        $nonce = $manager->create('my-form');
        $this->assertEquals(
            (new \DateTimeImmutable('2018-03-30 13:00'))
                ->getTimestamp(),
            $nonce->getExpiration()->getTimestamp()
        );
    }

    /**
     * @covers \pedroac\nonce\NoncesManager::create
     * @covers \pedroac\nonce\NoncesManager::verify
     * @covers \pedroac\nonce\NoncesManager::__construct
     */
    public function testVerify()
    {
        $manager = new NoncesManager(
            new FakeNoncesStorage,
            new FakeRandom,
            new \DateInterval('PT30M'),
            new \DateTimeImmutable('2018-03-30 12:00')
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
        $manager->create(
            'my-form-2',
            new \DateTimeImmutable('2018-03-30 11:00')
        );
        $this->assertTrue(
            $manager->verify('my-form-1', 'qwerty123')
        );
    }

    /**
     * @covers \pedroac\nonce\NoncesManager::create
     * @covers \pedroac\nonce\NoncesManager::purge
     * @covers \pedroac\nonce\NoncesManager::__construct
     */
    public function testExpire()
    {
        $manager = new NoncesManager(
            $storage = new FakeNoncesStorage,
            new FakeRandom,
            null,
            new \DateTimeImmutable('2018-03-30 12:00')
        );
        $nonce = $manager->create('my-form');
        $this->assertSame($nonce, $storage->get('my-form'));
        $manager->expire('my-form');
        $this->assertSame(null, $storage->get('my-form'));
    }

    /**
     * @covers \pedroac\nonce\NoncesManager::create
     * @covers \pedroac\nonce\NoncesManager::purge
     * @covers \pedroac\nonce\NoncesManager::__construct
     */
    public function testPurge()
    {
        $manager = new NoncesManager(
            $storage = new FakeNoncesStorage,
            new FakeRandom,
            null,
            new \DateTimeImmutable('2018-03-30 12:00:00')
        );
        $nonce1 = $manager->create('my-form-1');
        $manager->create(
            'my-form-2',
            new \DateTimeImmutable('2018-03-30 11:00:00')
        );
        $manager->purge();
        $this->assertSame($nonce1, $storage->get('my-form-1'));
        $this->assertSame(null, $storage->get('my-form-2'));
    }
}

class FakeNoncesStorage implements \pedroac\nonce\StorageNonces
{
    private $array = array();

    public function store(Nonce $nonce)
    {
        $this->array[$nonce->getName()] = $nonce;
    }

    public function get(string $name): ?Nonce
    {
        if (!isset($this->array[$name])) {
            return null;
        }
        return $this->array[$name];
    }

    public function expire(string $name)
    {
        unset($this->array[$name]);
    }

    public function purge(\DateTimeInterface $now=null)
    {
        foreach ($this->array as $key => $nonce) {
            if ($nonce->isExpired($now)) {
                unset($this->array[$key]);
            }
        }
    }
}

class FakeRandom implements \pedroac\nonce\Random
{
    public function randomize(): string
    {
        return 'qwerty123';
    }
}