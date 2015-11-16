
Many algorithms are generic. That is, the way the algorithm functions is
independent of what the algorithm does to the underlying data. These generic
algorithms can be wrapped in a reusable function.

This package provides reusable, generic functions implemented using the
callable object pattern. You can nest these generic functions to solve more
complex problems. See below and the [docs][over1].

[over1]: http://haldayne-docs.readthedocs.org/en/latest/Fox/

[![Latest Stable Version](https://poser.pugx.org/haldayne/fox/v/stable.png)](https://packagist.org/packages/haldayne/fox) [![Build Status](https://travis-ci.org/haldayne/fox.png?branch=master)](https://travis-ci.org/haldayne/fox) [![Coverage Status](https://coveralls.io/repos/haldayne/fox/badge.png?branch=master)](https://coveralls.io/r/haldayne/fox?branch=master)
[![Monthly Downloads](https://poser.pugx.org/haldayne/fox/d/monthly.png)](https://packagist.org/packages/haldayne/fox)


# Installation

Install with composer: `php composer.phar require haldayne/fox 1.0.x-dev`

Requires PHP version 5.5.0 or higher.  No other PHP extensions are required.


# Select examples

**Retry a function until it either succeeds or reaches the maximum number of
attempts. Delay each attempt by an exponentially increasing time:**

```php
$retry = new \Haldayne\Fox\Retry(
    function ($src, $dst) {      // <---\
        return copy($src, $dst); // <----= this will be retried as necessary
    }                            // <---/
);
$ok = $retry('ssh2.sftp://user:pass@host/the/file', 'thefile');
if (! $ok) {
    die("Copy failed after {$retry->getAttempts()} attempts");
}
```


# Package contents and documentation

Extensive documentation is available at Read the Docs:

* [End-user Documentation][pack1]
* [API Documentation][pack2]
* [List of generic functions][pack3]

[pack1]: http://haldayne-docs.rtfd.org/en/latest/Fox/
[pack2]: http://haldayne.github.io/documentation/api/
[pack3]: http://haldayne-docs.readthedocs.org/en/latest/Fox/list-of-generic-functions/


# Miscellaneous

These algorithms can be composed together. The repository name `fox` is an
abbreviation for `f(x)` said "f of x", which harks back to these functions'
compositional ability.

Both [Go][misc1] and [Java][misc2] have implementations of the Retry function
with exponential backoff.

[misc1]: https://github.com/cenkalti/backoff
[misc2]: https://github.com/google/google-http-java-client
