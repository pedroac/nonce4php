<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Cache\Simple\FilesystemCache;
use \pedroac\nonce\NoncesManager;

$nonce = null;
$isValidForm = false;
$isValidToken = false;
$wasSubmitted = filter_has_var(INPUT_POST, 'myform');
$inputNumber = filter_input(INPUT_POST, 'number') ?? '';
$tokenName = filter_input(INPUT_POST, 'token_name');
$tokenValue = filter_input(INPUT_POST, 'token_value') ?? '';

/**
 * Instantiate the nonces manager using a files system cache.
 */
$manager = new NoncesManager(new FilesystemCache);

/**
 * Validate the submitted token and remove the nonce.
 */
if ($tokenName) {
    $isValidToken = $manager->verifyAndVerify($tokenName, $tokenValue);
}
if ($wasSubmitted && $isValidToken) {
    $isValidForm = is_numeric($inputNumber);
}

/**
 * Generate a nonce.
 */
if (!$wasSubmitted || (!$isValidForm && $isValidToken)) {
    $nonce = $manager->create($tokenName);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Page Title</title>
    </head>
    <body>
        <?php if ($nonce) : ?>
            <?php if ($wasSubmitted && !$isValidForm) : ?>
                <p>Invalid input!</p>
            <?php endif; ?>
            <form method="POST">
                Number:
                <input type="text"
                        name="number"
                        value="<?= $inputNumber ?>" />
                <input type="hidden"
                       name="token_name"
                       value="<?= htmlspecialchars($nonce->getName()) ?>" />
                <input type="hidden"
                       name="token_value"
                       value="<?= htmlspecialchars($nonce->getValue()) ?>" />
                <input type="submit" name="myform" value="Submit" />
            </form>
        <?php elseif (!$isValidToken) : ?>
            <p>Invalid token!</p>
        <?php elseif ($isValidForm) : ?>
            <p>Success! Resending the form will throw an error.</p>
        <?php else : ?>
            <p>Unexpected state!</p>
        <?php endif; ?>
    </body>
</html>