<?php

namespace pedroac\nonce;

use PHPUnit\Framework\TestCase;
use pedroac\nonce\Random\HexRandomizer;

/**
 * HexBytesRandomizer class unit tests.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class HexRandomizerTest extends TestCase
{
    /**
     * @covers \pedroac\nonce\Random\HexRandomizer::__construct
     */
    public function testConstructZeroLengthException()
    {
        $this->expectException(\LengthException::class);
        new HexRandomizer(0);
    }

    /**
     * @covers \pedroac\nonce\Random\HexRandomizer::__construct
     */
    public function testConstructNegativeLengthException()
    {
        $this->expectException(\LengthException::class);
        new HexRandomizer(-1);
    }

    /**
     * @covers \pedroac\nonce\Random\HexRandomizer::randomize
     * @covers \pedroac\nonce\Random\HexRandomizer::__construct
     */
    public function testRandomizeEvenLength()
    {
        $this->assertRegExp(
            '~^[a-f0-9]{2}$~',
            (new HexRandomizer(2))->randomize()
        );
        $this->assertRegExp(
            '~^[a-f0-9]{8}$~',
            (new HexRandomizer(8))->randomize()
        );
        $this->assertRegExp(
            '~^[a-f0-9]{16}$~',
            (new HexRandomizer(16))->randomize()
        );
        $this->assertRegExp(
            '~^[a-f0-9]{32}$~',
            (new HexRandomizer(32))->randomize()
        );
    }

    /**
     * @covers \pedroac\nonce\Random\HexRandomizer::randomize
     * @covers \pedroac\nonce\Random\HexRandomizer::__construct
     */
    public function testRandomizeOddLength()
    {
        $this->assertRegExp(
            '~^[a-f0-9]{1}$~',
            (new HexRandomizer(1))->randomize()
        );
        $this->assertRegExp(
            '~^[a-f0-9]{7}$~',
            (new HexRandomizer(7))->randomize()
        );
        $this->assertRegExp(
            '~^[a-f0-9]{15}$~',
            (new HexRandomizer(15))->randomize()
        );
        $this->assertRegExp(
            '~^[a-f0-9]{31}$~',
            (new HexRandomizer(31))->randomize()
        );
    }
}