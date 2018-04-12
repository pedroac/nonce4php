<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Cache\Simple\FilesystemCache;
use \pedroac\nonce\NoncesManager;

$nonce = null;
$isValid = null;
$wasSubmitted = filter_has_var(INPUT_POST, 'myform');
$tokenName = filter_input(INPUT_POST, 'token_name');
$tokenValue = filter_input(INPUT_POST, 'token_value');

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
    $manager->expire($tokenName);
}

/**
 * Generate a nonce if the form was not submit or the submitted 
 * token is valid.
 */
if (!$wasSubmitted) {
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
        <?php if ($wasSubmitted) : ?>
            <?php if ($isValid) : ?>
                <p>Sucess!</p>
            <?php else : ?>
                <p>Invalid token!</p>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($nonce) : ?>
            <form method="POST">
                <input type="hidden"
                       name="token_name"
                       value="<?= htmlspecialchars($nonce->getName()) ?>" />
                <input type="hidden"
                       name="token_value"
                       value="<?= htmlspecialchars($nonce->getValue()) ?>" />
                <input type="submit" name="myform" value="Submit" />
            </form>
        <?php endif; ?>
    </body>
</html>