<?php
/**
 * The HtmlNonceField class.
 */

namespace pedroac\nonce\Form;

/**
 * A class to generate an hidden HTML input element with the 
 * token name and value that might be used to be verified.  
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class HtmlNonceField
{
    /**
     * The nonce form.
     *
     * @var NonceForm
     */
    private $form;

    /**
     * Create an instance that generates an hidden HTML input element with the 
     * token name and value from a nonce form.
     *
     * @param NonceForm $form The nonce form.
     */
    public function __construct(NonceForm $form)
    {
        $this->form = $form;
    }

    /**
     * Return the hidden HTML input element with the token name and value.
     *
     * @return string The HTML element.
     */
    public function __toString()
    {
        $htmlFieldName = htmlspecialchars($this->form->getFieldName());
        $htmlFieldValue = htmlspecialchars($this->form->getFieldValue());
        return
            "<input type=\"hidden\" "
            . "name=\"$htmlFieldName\" value=\"$htmlFieldValue\" />";
    }
}