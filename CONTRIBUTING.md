# Contributing

Thank you for considering contributing to the pedro/nonce open source PHP library.

## Reporting issues

- If you find a bug or other issues, please send a report [here](https://github.com/pedroac/nonce4php/issues).
- Provide sufficient instructions in order to be able to reproduce the issues as easier as possible.
- Explain what was expected and what actually happened.

## Developing

- Follow the [PSR-1](https://www.php-fig.org/psr/psr-1/) and [PSR-2](https://www.php-fig.org/psr/psr-2/) coding style rules.
- Use tools to enforce a consistent coding style and good practices.
- PMD and Code Sniffer are used to detect issues automatically.
- Make sure the tests suite passes:  
`php/vendor/bin/phpunit php/tests/ -c php/tests/configuration.xml`
- If you added code that should be tested, write unit tests.
- Check the code coverage: `php/tests/coverage-html`
- Classes, functions and interfaces should be documented.
- Make sure the README.md and other documentation is kept-updated.
- You may automatically generate an HTML documention using PHPDoc:
`./php/vendor/phpdocumentor/phpdocumentor/bin/phpdoc -d src/ -t docs/ --visibility=public --title="Documentation"`
- Git commit comments should follow the [conventions](https://chris.beams.io/posts/git-commit/).
- By contributing, you agree that your contributions will be licensed under its MIT License.
