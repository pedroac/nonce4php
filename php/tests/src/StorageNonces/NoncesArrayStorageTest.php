<?php

namespace pedroac\nonce;

use PHPUnit\Framework\TestCase;
use pedroac\nonce\StorageNonces\NoncesArrayStorage;
use pedroac\nonce\Nonce;

/**
 * NoncesArrayStorage class unit tests.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class NoncesArrayStorageTest extends TestCase
{
    /**
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::store
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::get
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::__construct
     */
    public function testStoreGet()
    {
        $data = array();
        $storage = new NoncesArrayStorage($data);
        $storage->store(
            $nonce = new Nonce(
                'my-form',
                'qwerty123',
                new \DateTimeImmutable
            )
        );
        $this->assertSame($nonce, $storage->get('my-form'));
        $this->assertSame($nonce, $data['my-form']);
    }

    /**
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::store
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::get
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::expire
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::__construct
     */
    public function testStoreExpire()
    {
        $data = array();
        $storage = new NoncesArrayStorage($data);
        $storage->store(
            new Nonce(
                'my-form-1',
                'qwerty123',
                new \DateTimeImmutable
            )
        );
        $storage->store(
            $nonce2 = new Nonce(
                'my-form-2',
                'qwerty123',
                new \DateTimeImmutable
            )
        );
        $storage->expire('my-form-1');
        $this->assertSame(null, $storage->get('my-form-1'));
        $this->assertFalse(isset($data['my-form-1']));
        $this->assertSame($nonce2, $storage->get('my-form-2'));
        $this->assertSame($nonce2, $data['my-form-2']);
    }

    /**
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::store
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::get
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::purge
     * @covers \pedroac\nonce\StorageNonces\NoncesArrayStorage::__construct
     */
    public function testStorePurge()
    {
        $data = array();
        $storage = new NoncesArrayStorage($data);
        $storage->store(
            $nonce1 = new Nonce(
                'my-form-1',
                'qwerty123',
                new \DateTimeImmutable('2018-03-30 13:00:00')
            )
        );
        $storage->store(
            $nonce2 = new Nonce(
                'my-form-2',
                'qwerty123',
                new \DateTimeImmutable('2018-03-30 13:00:00')
            )
        );
        $storage->store(
            new Nonce(
                'my-form-3',
                'qwerty123',
                new \DateTimeImmutable('2018-03-30 11:00:00')
            )
        );
        $storage->store(
            new Nonce(
                'my-form-4',
                'qwerty123',
                new \DateTimeImmutable('2018-03-30 10:00:00')
            )
        );
        $storage->purge(new \DateTimeImmutable('2018-03-30 12:00:00'));
        $this->assertSame($nonce1, $storage->get('my-form-1'));
        $this->assertSame($nonce2, $storage->get('my-form-2'));
        $this->assertSame(null, $storage->get('my-form-3'));
        $this->assertSame(null, $storage->get('my-form-4'));
    }
}