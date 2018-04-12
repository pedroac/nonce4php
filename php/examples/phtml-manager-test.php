<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Cache\Simple\FilesystemCache;
use \pedroac\nonce\NoncesManager;

session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = rand(1, 9999);
}
$user_id = $_SESSION['user_id'];

$nonce = null;
$isValidForm = false;
$isValidToken = false;
$wasSubmitted = filter_has_var(INPUT_POST, 'myform');
$inputNumber = filter_input(INPUT_POST, 'number') ?? '';
$tokenName = "{$user_id}_form";
$tokenValue = filter_input(INPUT_POST, $tokenName) ?? '';

/**
 * Instantiate a nonces manager using a files system cache.
 */
$manager = new NoncesManager(new FilesystemCache);

/**
 * When the form is submitted, validate the submitted 
 * value and remove the nonce.
 */
if ($wasSubmitted) {
    $isValidToken = $manager->verifyAndExpire($tokenName, $tokenValue);
    if ($isValidToken) {
        $isValidForm = is_numeric($inputNumber);
    }
}

/**
 * Generate a nonce if the form was not submit or the submitted 
 * input is not valid.
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
                       name="<?= htmlspecialchars($tokenName) ?>"
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