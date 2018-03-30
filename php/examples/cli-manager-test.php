<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();
$manager = new \pedroac\nonce\NoncesManager(
    new \pedroac\nonce\StorageNonces\NoncesArrayStorage($_SESSION),
    new \pedroac\nonce\Random\HexRandomizer(32)
);
$nonce = $manager->create('action');
$value = $nonce->getValue();
echo "Value: $value\n";

echo $manager->verify('action', $value) ? 'VALID' : 'INVALID', "\n";
echo $manager->verify('action', 'wrong!!!') ? 'VALID' : 'INVALID', "\n";
$manager->expire('action');
echo $manager->verify('action', $value) ? 'VALID' : 'INVALID', "\n";