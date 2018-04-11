<?php

namespace pedroac\nonce\Form;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Simple\ArrayCache;
use \pedroac\nonce\NoncesManager;
use pedroac\nonce\Nonce;

/**
 * HtmlNonceField class unit tests.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class HtmlNonceFieldTest extends TestCase
{
    /**
     * @covers pedroac\nonce\Form\HtmlNonceField::__construct
     * @covers pedroac\nonce\Form\HtmlNonceField::__toString
     */
    public function testToString()
    {
        $form = new NonceForm(
            'token',
            new NoncesManager(new ArrayCache(60))
        );
        
        $this->assertRegExp(
            '~\<input (.+(?=/>))/>~',
            (string)new HtmlNonceField($form)
        );

        $expected = new \DOMDocument;
        $expected->loadXml(
            '<input type="hidden" name="token"'
            . ' value="' . $form->getFieldValue() . '" />'
        );
        $actual = new \DOMDocument;
        $actual->loadXml((string)new HtmlNonceField($form));
        
        $this->assertEqualXMLStructure(
            $expected->firstChild,
            $actual->firstChild,
            true
        );
    }
}