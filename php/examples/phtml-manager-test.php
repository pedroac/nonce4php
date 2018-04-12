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
$isValid = false;
$wasSubmitted = filter_has_var(INPUT_POST, 'myform');
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
if (!$wasSubmitted) {
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
                       name="<?= htmlspecialchars($tokenName) ?>"
                       value="<?= htmlspecialchars($nonce->getValue()) ?>" />
                <input type="submit" name="myform" value="Submit" />
            </form>
        <?php endif; ?>
    </body>
</html>