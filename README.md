
Many algorithms are generic. That is, the way the algorithm functions is
independent of what the algorithm does to the underlying data. Let's take a
specific example: `array_sum` and `implode`.  How do these function?  They
iterate over an array, building up a return value.  Is there another PHP
function that does that?  Yes, that's `array_reduce` and there's an
equivalence:

| Specific Function | Generic equivalent |
| ----------------- | ------------------ |
| `array_sum($a)` | `array_reduce($a, function ($t, $a) { return $t + $a; })` |
| `implode(',', $a)` | `array_reduce(array_slice($a, 1), function ($t, $a) { return $t . ',' . $a; }, $a[0])` |

You wouldn't do this in real life, but it demonstrates the concept of higher-
order functions: one function taking another so as to abstract common behavior
to one place.  As program designers, we want our language powerful enough to
express the *concept of summation*, not just provide functions to compute sums.

This package provides higher-order functions. All are implemented using the
callable object pattern, which means you create an object implementing the
generic behavior and passing it the specific function, then invoke it with
parentheses. This approach allows you to set state before, and get state after,
calling the algorithm:

```php
// call some code, capturing PHP triggered errors locally
use \Haldayne\Fox\CaptureErrors;
$helper = new CaptureErrors(function ($input) { trigger_error('Oi!'); return $input; });
$helper->setCapturedErrorTypes(E_ALL|~E_STRICT);
$result = $helper(false);
if (! $result) {
    die($helper->getCapturedErrors()->pop()->get('message'));
}
```


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

All functions implemented as [callable classes][pack3]. Classes included in
this package `\Haldayne\Fox\`:

| Class | Description |
|-------|-------------|
| `CaptureErrors` | Calls a function, capturing all PHP errors raised during the execution of the function. Provides access to the queue of captured errors afterwards. |
| `Expression` | Converts a string containing a PHP expression into a callable. Useful when you want to build small functions without the overhead of PHP closure syntax. |
| `Retry` | Retries a function until the function succeeds or fails the retry strategy. The default retry strategy attempts 5 calls and exponentially increases the delay between each call. You can define your own retry strategy. |
| `Y` | Implements the [Y-combinator][pack4], which is a way to express a recursive algorithm with neither recursion nor iteration. |

[pack1]: http://haldayne-docs.rtfd.org/
[pack2]: http://haldayne.github.io/documentation/api/
[pack3]: http://php.net/manual/en/language.oop5.magic.php#object.invoke 
[pack4]: http://matt.might.net/articles/implementation-of-recursive-fixed-point-y-combinator-in-javascript-for-memoization/


# Miscellaneous

These algorithms can be composed together. The repository name `fox` is an
abbreviation for `f(x)` said "f of x", which harks back to these functions'
compositional ability.

Both [Go][misc1] and [Java][misc2] have implementations of the Retry function
with exponential backoff.

[misc1]: https://github.com/cenkalti/backoff
[misc2]: https://github.com/google/google-http-java-client
