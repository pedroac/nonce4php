<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Cache\Simple\FilesystemCache;
use \pedroac\nonce\NoncesManager;
use \pedroac\nonce\Form\HtmlNonceField;
use \pedroac\nonce\Form\NonceForm;

/**
 * Create a nonce form manager.
 */
$form = new NonceForm(
    'token',
    new NoncesManager(new FilesystemCache)
);
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
        <!-- If a submitted token is valid... -->
        <?php if ($form->wasSubmittedValid()) : ?>
            <p>Success!</p>
        <!-- Otherwise, it wasnt't submitted or it's invalid ...-->
        <?php else: ?>
            <!-- If it's invalid... -->
            <?php if ($form->wasSubmittedInvalid()) : ?>
                <p>Invalid token!</p>
            <?php endif; ?>
            <form method="POST">
                <?= $htmlField ?>
                <input type="submit" name="myform" value="Submit" />
            </form>
        <?php endif; ?>
    </body>
</html>