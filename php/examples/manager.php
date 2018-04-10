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
if (isset($_POST['token'])) {
    $validToken = $manager->verify($nonce, $_POST['token']);
    $manager->expire('action');
}
