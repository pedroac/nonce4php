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
$isValid = null;
$tokenName = "{$user_id}_form";
$tokenValue = filter_input(INPUT_POST, $tokenName);

/**
 * Instantiate a nonces manager using a files system cache.
 */
$manager = new NoncesManager(new FilesystemCache);

/**
 * When the form is submitted, validate the submitted 
 * value and remove the nonce.
 */
if ($tokenValue) {
    $isValid = $manager->verify($tokenName, $tokenValue);
    $manager->expire($tokenName);
}

/**
 * Generate a nonce if the form was not submit or the submitted 
 * value was not valid.
 */
if (!$isValid) {
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
            <?php if ($isValid === false) : ?>
                <p>Invalid!</p>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden"
                    name="<?= htmlspecialchars($nonce->getName()) ?>"
                    value="<?= htmlspecialchars($nonce->getValue()) ?>" />
                <input type="submit" name="myform" value="Submit" />
            </form>
        <?php elseif ($isValid): ?>
            <p>Success!</p>
        <?php endif; ?>
    </body>
</html>