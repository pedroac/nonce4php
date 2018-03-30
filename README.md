# nonce4php

A nonce manager PHP library usefull for preventing CSRF attacks.  
The nonces generator and storage can be customized and selected.

## Prerequisites

- PHP 7.1 or later: http://php.net/downloads.php
- Composer: https://getcomposer.org

## Installing

Add to your `composer.json` file:

```json
"repositories": [
    {
        "url": "https://github.com/pedroac/nonces4php.git",
        "type": "vcs"
    }
],
"require": {
    "pedroac/nonces4php": "@stable"
}
```

## Usage

Instantiate a nonce manager:
```php
<?php
require __DIR__ . '/../vendor/autoload.php';

session_start();
$manager = new \pedroac\nonce\NoncesManager(
    new \pedroac\nonce\StorageNonces\NoncesArrayStorage($_SESSION),
    new \pedroac\nonce\Random\HexRandomizer(32),
    new \DateInterval('PT1H')
);
```

Generate a nonce:
```php
$nonce = $manager->create('_form-nc');
```

Use the nonce name and value to build, for instance, a HTML form:
```php
<input type="hidden"
       name="_form-nc"
       value="<?= htmlspecialchars($nonce->getValue()) ?>" />
```

When the form is submitted, validate the submitted value and remove the nonce:
```php
$isValid = $manager->verify('action', $_POST['_form-nc']);
$manager->expire('action');
```

Unusable nonces might be removed periodically using a crong job:
```php
$manager->purge();
```

## Examples

- [CLI test](php/examples/cli-manager-test.php)
- [HTML Form test](php/examples/phtml-manager-test.php)

## Running the tests

Run in the libraries's root directory:

`php/vendor/bin/phpunit php/tests/ -c php/tests/configuration.xml`

If the tests were successful, `php/tests/coverage-html` should have the code coverage report.

## Versioning

It should be used [SemVer](http://semver.org/) for versioning.

## Authors

- Pedro Amaral Couto - Initial work - https://github.com/pedroac

## License

pedroac/nonces4php is released under the MIT public license.  
See the enclosed [LICENSE](LICENSE) for details.

## Acknowledgements

The library was developed as a private request response made by a Stackoverflow user.