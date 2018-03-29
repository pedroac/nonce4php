# nonces4php

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

TODO

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