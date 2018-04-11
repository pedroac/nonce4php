<?php
/**
 * The NonceForm class.
 */

namespace pedroac\nonce\Form;
use pedroac\nonce\NoncesManager;
use pedroac\nonce\Nonce;

/**
 * A nonce form handler.
 * 
 * A class that should make easier to handle nonces related to forms.
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class NonceForm extends NoncesManager
{
    /**
     * The nonces manager used to generate a nonce, store it and verify tokens
     * against it.
     *
     * @var NoncesManager
     */
    private $manager;

    /**
     * The form field name.
     *
     * @var string
     */
    private $fieldName;

    /**
     * The input variables. It should be an associative array.
     *
     * @var array
     */
    private $inputVars;

    /**
     * The submitted token name.
     * It should be NULL if the token wasn't submitted. 
     *
     * @var string|null
     */
    private $submittedName = null;

    /**
     * The submitted token value.
     * It should be NULL if the token wasn't submitted. 
     *
     * @var string|null
     */
    private $submittedValue = null;

    /**
     * Is it a valid token?
     * It should be NULL if the token wasn't yet validated.
     *
     * @var bool|null
     */
    private $isValid = null;

    /**
     * A generated nonce used to build a token.
     * It should be NULL if the nonce wasn't yet generated.
     *
     * @var Nonce|null
     */
    private $nonce = null;

    /**
     * Create a nonce form handler instance.
     *
     * @param string $fieldName The form field name.
     * @param NoncesManager $manager The nonces manager used to generate a nonce, 
     * store it and verify tokens against it.
     * @param array $inputVars The input variables. $_POST should be the default.
     */
    public function __construct(
        string $fieldName,
        NoncesManager $manager,
        array $inputVars = null
    ) {
        if (!$inputVars) {
            $inputVars = filter_input_array(INPUT_POST);
        }
        $this->fieldName = $fieldName;
        $this->manager = $manager;
        $this->inputVars = $inputVars;
    }

    /**
     * Return the nonce field name.
     *
     * @return string The field name.
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * Return the nonce field value.
     *
     * @return string The field value.
     */
    public function getFieldValue(): string
    {
        $nonce = $this->getNewNonce();
        return $nonce->getName() . ':' . $nonce->getValue();
    }

    /**
     * Return the submitted token name that is supposedly a nonce name.
     * 
     * If the token wasn't submitted, then it should return NULL.
     *
     * @return string|null The sent token name.
     */
    public function getSubmittedName(): ?string
    {
        if (!$this->submittedName) {
            $this->updateSubmitted();
        }
        return $this->submittedName;
    }

    /**
     * Return the submitted token value that is supposedly the nonce value.
     *
     * If the token wasn't submitted, then it should return NULL.
     * 
     * @return string|null The submitted token value.
     */
    public function getSubmittedValue(): ?string
    {
        if (!$this->submittedValue) {
            $this->updateSubmitted();
        }
        return $this->submittedValue;
    }

    /**
     * Check if the token was submitted.
     *
     * @return boolean Was the token submitted?
     */
    public function wasSubmitted(): bool
    {
        return isset($this->inputVars[$this->fieldName]);
    }

    /**
     * Check if it was submitted an invalid token.
     * 
     * It should return FALSE if the token wasn't submmited.
     * 
     * It should only return TRUE if the token was submitted and
     * it it's invalid.
     * 
     * The nonce with the same submitted token name should be expired.
     * 
     * It should always return the same value.
     *
     * @return boolean Is the submitted token invalid?
     */
    public function isSubmittedInvalid(): bool
    {
        return $this->wasSubmitted() && !$this->isSubmittedValid();
    }

    /**
     * Check if it was submitted a valid token.
     * 
     * It should return FALSE if the token wasn't submmited.
     * 
     * It should only return TRUE if the token was submitted and
     * it was succesfully verified.
     * 
     * The nonce with the same submitted token name should be expired.
     * 
     * It should always return the same value.
     *
     * @return boolean Is the submitted token valid?
     */
    public function isSubmittedValid(): bool
    {
        if ($this->isValid !== null) {
            return $this->isValid;
        }
        if (!$this->wasSubmitted()) {
            $this->isValid = false;
            return false;
        }
        $this->isValid = $this->manager->verify(
            $this->getSubmittedName(),
            $this->getSubmittedValue()
        );
        $this->manager->expire($this->getSubmittedName());
        return $this->isValid;
    }

    /**
     * Return a new generated nonce.
     * 
     * It should always return the same nonce.
     * 
     * It's not a nonce that was previous generated which might be used
     * to validate the submitted token.
     *
     * @return Nonce The nonce.
     */
    public function getNewNonce(): Nonce
    {
        if (!$this->nonce) {
            $this->nonce = $this->manager->create();
            assert(strpos($this->nonce->getName(), ':') === false);
        }
        return $this->nonce;
    }

    /**
     * Set the $submittedName and $ubmittedValue properties.
     *
     * @return void
     */
    private function updateSubmitted()
    {
        $submittedValue = $this->inputVars[$this->fieldName] ?? null;
        if (!$submittedValue) {
            $this->submittedName = null;
            $this->submittedValue = null;
            return;
        }
        $parts = explode(':', $submittedValue, 2);
        $this->submittedName = $parts[0] ?? '';
        $this->submittedValue = $parts[1] ?? '';
    }
}