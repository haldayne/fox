
Many algorithms are generic. That is, the way the algorithm functions is
independent of what the algorithm does to the underlying data. These generic
algorithms can be wrapped in a reusable function.

This package provides reusable, generic functions implemented using the
callable object pattern. You can nest these generic functions to solve more
complex problems. See below and the [docs][over1].

[over1]: http://haldayne-docs.readthedocs.org/en/latest/fox/

[![Latest Stable Version](https://poser.pugx.org/haldayne/fox/v/stable.png)](https://packagist.org/packages/haldayne/fox)
[![Build Status](https://travis-ci.org/haldayne/fox.png?branch=master)](https://travis-ci.org/haldayne/fox)
[![Coverage Status](https://coveralls.io/repos/haldayne/fox/badge.png?branch=master&bust=1)](https://coveralls.io/r/haldayne/fox?branch=master)
[![Monthly Downloads](https://poser.pugx.org/haldayne/fox/d/monthly.png)](https://packagist.org/packages/haldayne/fox)


# Installation

Install with composer: `php composer.phar require haldayne/fox ^1.0`

Requires PHP version 5.5.0 or higher.  No other PHP extensions are required.


# Select examples

**Retry a function until it either succeeds or reaches the maximum number of
attempts. Delay each attempt by an exponentially increasing time:**

```php
$retry = new \Haldayne\Fox\Retry(
    function ($src, $dst) { return copy($src, $dst); }
);
$retry->setAttempts(3);
$retry('ssh2.sftp://user:pass@host/the/file', 'thefile')
    or die("Copy failed after {$retry->getAttempts()} attempts");
```


# Package contents and documentation

Extensive documentation is available at Read the Docs:

* [End-user Documentation][pack1]
* [API Documentation][pack2]
* [List of generic functions][pack3]

[pack1]: http://haldayne-docs.rtfd.org/en/latest/Fox/
[pack2]: http://haldayne.github.io/documentation/api/
[pack3]: http://haldayne-docs.readthedocs.org/en/latest/Fox/list-of-generic-functions/


# Contributing

Your contribution is welcome! [Open an issue][contrib1] or [create a gist][contrib2]
showing how you're using fox.  If you want to [add a new algorithm][contrib3]
to fox, please keep these in mind:

* Please fork the repository and create a PR.
* Use [PSR-2][contrib4].
* Update tests so that you have at least 80% coverage.
* Choose something useful. If the algorithm takes many arguments, or exposes
state after calculating a result, it's a good candidate.

[contrib1]: https://github.com/haldayne/fox/issues
[contrib2]: https://gist.github.com/
[contrib3]: https://github.com/haldayne/fox/pulls
[contrib4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md


# Miscellaneous

These algorithms can be composed together. The repository name `fox` is an
abbreviation for `f(x)` said "f of x", which harks back to these functions'
compositional ability.

Both [Go][misc1] and [Java][misc2] have implementations of the Retry function
with exponential backoff.

[misc1]: https://github.com/cenkalti/backoff
[misc2]: https://github.com/google/google-http-java-client
