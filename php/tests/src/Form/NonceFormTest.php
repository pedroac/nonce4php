<?php

namespace pedroac\nonce\Form;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Simple\ArrayCache;
use \pedroac\nonce\NoncesManager;
use pedroac\nonce\Nonce;

/**
 * NonceForm class unit tests.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class NonceFormTest extends TestCase
{
    /**
     * @covers pedroac\nonce\Form\NonceForm::__construct
     * @covers pedroac\nonce\Form\NonceForm::getFieldName
     */
    public function testGetFieldName()
    {
        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60))
        );
        $this->assertEquals(
            'token',
            $form->getFieldName()
        );
    }

    /**
     * @covers pedroac\nonce\Form\NonceForm::__construct
     * @covers pedroac\nonce\Form\NonceForm::getFieldValue
     * @covers pedroac\nonce\Form\NonceForm::getNewNonce
     */
    public function testGetFieldValue()
    {
        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60))
        );
        $this->assertRegExp(
            '~^[^:]+:.+~',
            $form->getFieldValue()
        );
        $nonce = $form->getNewNonce();
        $this->assertEquals(
            $nonce->getName() . ':' . $nonce->getValue(),
            $form->getFieldValue()
        );
    }

    /**
     * @covers pedroac\nonce\Form\NonceForm::__construct
     * @covers pedroac\nonce\Form\NonceForm::getSubmittedName
     * @covers pedroac\nonce\Form\NonceForm::updateSubmitted
     */
    public function testGetSubmittedName()
    {
        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60)),
            ['token' => '12345:67890']
        );
        $this->assertEquals(
            '12345',
            $form->getSubmittedName()
        );

        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60))
        );
        $this->assertNull(
            $form->getSubmittedName()
        );
    }

    /**
     * @covers pedroac\nonce\Form\NonceForm::__construct
     * @covers pedroac\nonce\Form\NonceForm::getSubmittedValue
     * @covers pedroac\nonce\Form\NonceForm::updateSubmitted
     */
    public function testGetSubmittedValue()
    {
        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60)),
            ['token' => '12345:67890']
        );
        $this->assertEquals(
            '67890',
            $form->getSubmittedValue()
        );

        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60))
        );
        $this->assertNull(
            $form->getSubmittedValue()
        );
    }

    /**
     * @covers pedroac\nonce\Form\NonceForm::__construct
     * @covers pedroac\nonce\Form\NonceForm::wasSubmitted
     */
    public function testWasSubmitted()
    {
        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60)),
            ['token' => '12345:67890']
        );
        $this->assertTrue(
            $form->wasSubmitted()
        );

        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60))
        );
        $this->assertFalse(
            $form->wasSubmitted()
        );
    }

    /**
     * @covers pedroac\nonce\Form\NonceForm::__construct
     * @covers pedroac\nonce\Form\NonceForm::isSubmittedValid
     */
    public function testIsSubmittedValid()
    {
        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60))
        );
        $this->assertFalse(
            $form->isSubmittedValid()
        );
        
        $cache = new ArrayCache(60);
        $form = new NonceForm(
            'token',
            new NoncesManager($cache)
        );
        $value = $form->getFieldValue();

        $form = new NonceForm(
            'token',
            new NoncesManager($cache),
            ['token' => $value]
        );
        $this->assertTrue(
            $form->isSubmittedValid()
        );
        $this->assertTrue(
            $form->isSubmittedValid()
        );

        $form = new NonceForm(
            'token',
            new NoncesManager($cache),
            ['token' => '12345']
        );
        $this->assertFalse(
            $form->isSubmittedValid()
        );
    }

    /**
     * @covers pedroac\nonce\Form\NonceForm::__construct
     * @covers pedroac\nonce\Form\NonceForm::isSubmittedInvalid
     */
    public function testIsSubmittedInvalid()
    {
        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60))
        );
        $this->assertFalse(
            $form->isSubmittedInvalid()
        );
        
        $cache = new ArrayCache(60);
        $form = new NonceForm(
            'token',
            new NoncesManager($cache)
        );
        $value = $form->getFieldValue();

        $form = new NonceForm(
            'token',
            new NoncesManager($cache),
            ['token' => $value]
        );
        $this->assertFalse(
            $form->isSubmittedInvalid()
        );
        $this->assertFalse(
            $form->isSubmittedInvalid()
        );

        $form = new NonceForm(
            'token',
            new NoncesManager($cache),
            ['token' => '12345']
        );
        $this->assertTrue(
            $form->isSubmittedInvalid()
        );
    }
}