<?php
require __DIR__ . '/../vendor/autoload.php';
session_start();
$manager = new \pedroac\nonce\NoncesManager(
    new \pedroac\nonce\StorageNonces\NoncesArrayStorage($_SESSION),
    new \pedroac\nonce\Random\HexRandomizer(32)
);
$nonce = null;
$isValid = null;
if (isset($_POST['myform'])) {
    $isValid =
        isset($_POST['_nc'])
        && $manager->verify('_nc', $_POST['_nc']);
}
if (!$isValid) {
    $nonce = $manager->create('_nc');
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
            <form method="POST">
                <input type="hidden"
                    name="_nc"
                    value="<?= htmlspecialchars($nonce->getValue()) ?>" />
                <input type="submit" name="myform" value="Submit" />
            </form>
        <?php elseif ($isValid): ?>
            <p>Success!</p>
        <?php endif; ?>
    </body>
</html>