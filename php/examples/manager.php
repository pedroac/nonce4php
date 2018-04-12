<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Cache\Simple\ArrayCache;
use \pedroac\nonce\NoncesManager;
use \pedroac\nonce\Random\HexRandomizer;

$manager = new NoncesManager(
    new ArrayCache(60),
    new HexRandomizer(32)
);
$nonce = $manager->create('action');
$value = $nonce->getValue();

$validToken = false;
if (filter_has_var(INPUT_POST, 'action')) {
    $validToken = $manager->verifyAndExpire(
        'action',
        filter_input(INPUT_POST, 'action')
    );
}
