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
echo "Value: $value\n";

echo (
    $manager->verify('action', $value)
        ? 'VALID'
        : 'INVALID'
    ), "\n";
echo (
    $manager->verify('action', 'wrong!!!')
        ? 'VALID'
        : 'INVALID'
    ), "\n";
$manager->expire('action');
echo (
    $manager->verify('action', $value) 
        ? 'VALID' 
        : 'INVALID'
    ), "\n";