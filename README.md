# pedroac/nonce for PHP

[![Build Status](https://travis-ci.org/pedroac/nonce4php.svg?branch=master)](https://travis-ci.org/pedroac/nonce4php)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d099b114ef4f4d99bb1f39a8064aa8c4)](https://app.codacy.com/app/pedroac/nonce4php?utm_source=github.com&utm_medium=referral&utm_content=pedroac/nonce4php&utm_campaign=badger)
[![Support via PayPal](https://img.shields.io/badge/Donate-PayPal-green.svg)](http://paypal.me/pedroac)

A nonce manager PHP library useful for preventing CSRF attacks.  
The nonces generator and storage can be customised, extendable and selected.

## Prerequisites

- PHP 7.1 or later: http://php.net/downloads.php
- Composer: https://getcomposer.org
- At least one PSR-16 implementation. Examples:
  - [symfony/cache](https://packagist.org/packages/symfony/cache)
  - [matthiasmullie/scrapbook](https://packagist.org/packages/matthiasmullie/scrapbook)

## Installing

Run the command:

`composer require pedroac/nonce`

## Usage

### Examples

- [Using Symfony ArrayCache](php/examples/manager.php)
- [CLI test](php/examples/cli-manager-test.php)
- [HTML form using a session](php/examples/phtml-manager-test.php)
- [HTML form using an auto generated nonce name](php/examples/phtml-auto-nonce-name.php)
- [HTML form using a helper](php/examples/phtml-easy-form.php)

### HTML form with a token

1) Create a nonce form helper:
```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Cache\Simple\FilesystemCache;
use \pedroac\nonce\NoncesManager;
use \pedroac\nonce\Form\HtmlNonceField;
use \pedroac\nonce\Form\NonceForm;

// this handles automatically the input and nonce management
$form = new NonceForm(
    'token', // the HTML input name
    new NoncesManager(
      new FilesystemCache // a \Psr\SimpleCache\CacheInterface implementation
    )
);
// this will be used to generate a HTML input element
$htmlField = new HtmlNonceField($form);
```

2) Check if it was submitted a valid token and handle it:
```php
if ($form->wasSubmittedValid()) {
  // handle success
}
```

3) Check if it was submitted an invalid token and handle it:
```php
if ($form->wasSubmittedInvalid()) {
  // handle failure
}
```

4) Make the HTML form:
```php
<form method="POST">
    <?= $htmlField ?>
    <!-- more HTML -->
    <input type="submit" name="myform" value="Submit" />
</form>
```

The nonce is expired automatically when the token is verified with the `NonceForm` class.

### General usage

1) Instantiate a nonce manager:
```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Cache\Simple\FilesystemCache;
use \pedroac\nonce\NoncesManager;

$manager = new NoncesManager(new FilesystemCache);
```

2) Generate a nonce:
```php
$nonce = $manager->create();
```

3) Use the nonce name and value to build, for instance, a HTML form:
```php
<input type="hidden"
       name="token_name"
       value="<?= htmlspecialchars($nonce->getName()) ?>" />
<input type="hidden"
       name="token_value"
       value="<?= htmlspecialchars($nonce->getValue()) ?>" />
```

4) When the request is submitted, validate the submitted value and remove the nonce:
```php
$tokenName = filter_input(INPUT_POST, 'token_name']);
$tokenValue = filter_input(INPUT_POST, 'token_value']);

$isValid = $manager->verify($tokenName, $tokenValue);
$manager->expire($tokenName);
```

### Options

It's possible to select the random nonce value generator and the expire interval: 

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Cache\Simple\ArrayCache;
use \pedroac\nonce\NoncesManager;
use \pedroac\nonce\Random\HexRandomizer;

$manager = new NoncesManager(
    new ArrayCache(60),
    new HexRandomizer(32), // a \pedroac\nonce\Random implementation
    new \DateInterval('PT3H')
);
```

It's also possible to create a nonce with a specified name:

```php
$tokenName = "{$user_id}_form";
$nonce = $manager->create($tokenName);
```

NonceForm default input source is $_POST, but it accepts any array input:
```php
$form = new NonceForm(
    'token',
    new NoncesManager(
      new FilesystemCache
    ),
    filter_input_array(INPUT_GET) // use $_GET
);
```

## Running the tests

Run from the library root folder:

`php/vendor/bin/phpunit php/tests/ -c php/tests/configuration.xml`

If the tests were successful, `php/tests/coverage-html` should have the code coverage report.

## Versioning

It should be used [SemVer](http://semver.org/) for versioning.

## Authors

- Pedro Amaral Couto - Initial work - https://github.com/pedroac

## License

pedroac/nonce is released under the MIT public license.  
See the enclosed [LICENSE](LICENSE) for details.

## Acknowledgements

The library was developed as a private request response made by a Stackoverflow user.