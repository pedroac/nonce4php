<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Cache\Simple\FilesystemCache;
use \pedroac\nonce\NoncesManager;
use \pedroac\nonce\Form\HtmlNonceField;
use \pedroac\nonce\Form\NonceForm;

const STATE_NOT_SUBMITTED = 1;
const STATE_SUCCESS = 2;
const STATE_INVALID_INPUT = 3;
const STATE_INVALID_TOKEN = 4;
$isValidForm = false;
$state = STATE_NOT_SUBMITTED;
$inputNumber = filter_input(INPUT_POST, 'number');

/**
 * Create a nonce form manager.
 */
$form = new NonceForm(
    'token',
    new NoncesManager(new FilesystemCache)
);

/**
 * Validate form input.
 */
if ($form->isSubmittedInvalid()) {
    $state = STATE_INVALID_TOKEN;
} else if ($form->isSubmittedValid()) {
    $isValidForm = is_numeric($inputNumber);
    $state = $isValidForm ? STATE_SUCCESS : STATE_INVALID_INPUT;
}

/**
 * Create a an HTML nonce field generator.
 */
$htmlField = new HtmlNonceField($form);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Page Title</title>
    </head>
    <body>
        <?php if ($state == STATE_INVALID_TOKEN) : ?>
            <p>Invalid token!</p>
        <?php elseif ($state == STATE_SUCCESS) : ?>
            <p>Success! Resending the form will throw an error.</p>
        <?php else : ?>
            <?php if ($state == STATE_INVALID_INPUT) : ?>
                <p>Invalid input</p>
            <?php endif; ?>
            <form method="POST">
                Number:
                <input type="text"
                        name="number"
                        value="<?= $inputNumber ?>" />
                <?= $htmlField ?>
                <input type="submit" name="myform" value="Submit" />
            </form>
        <?php endif; ?>
    </body>
</html>