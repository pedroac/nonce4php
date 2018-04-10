<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Cache\Simple\FilesystemCache;
use \pedroac\nonce\NoncesManager;

$nonce = null;
$isValid = null;
$tokenName = filter_input('token_name', INPUT_POST);
$tokenValue = filter_input('token_value', INPUT_POST);

/**
 * Instantiate a nonces manager using a files system cache.
 */
$manager = new NoncesManager(new FilesystemCache);

/**
 * When the form is submitted, validate the submitted 
 * value and remove the nonce.
 */
if ($tokenName && $tokenValue) {
    $isValid = $manager->verify($tokenName, $tokenValue);
    $manager->expire();
}

/**
 * Generate a nonce if the form was not submit or the submitted 
 * value was not valid.
 */
if (!$isValid) {
    $nonce = $manager->create();
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
            <?php if ($isValid === false) : ?>
                <p>Invalid!</p>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden"
                    name="token_name"
                    value="<?= htmlspecialchars($nonce->getName()) ?>" />
                <input type="hidden"
                    name="token_value"
                    value="<?= htmlspecialchars($nonce->getValue()) ?>" />
                <input type="submit" name="myform" value="Submit" />
            </form>
        <?php elseif ($isValid): ?>
            <p>Success!</p>
        <?php endif; ?>
    </body>
</html>