
---TODO---
Every programmer knows how to compose functions, because we all do it every
day. Here's a silly example: `printf("I ate %d apples", count($apples);`. The
output of `count` becomes the input of `printf`. Simple, easy, and limited.
Why stop there?

We can write functions that take functions. This example may be familiar:
`array_map(function ($number) { return $number * 2; }, $numbers);` Here, the
outer `array_map` applies its own algorithm (iterating over the input array),
using the output of the inner function "callback" as a transformer. The 
composition happens inside the `array_map` algorithm.
---TODO---


# Installation

You need at least PHP 5.5.0.  No other extensions are required.

Install via composer: `php composer.phar require haldayne/fox 1.0.x-dev`


# Select examples

**Retry a function until it either succeeds or reaches the maximum number of
attempts. Delay each attempt by an exponentially increasing time:**

```
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
| `Retry` | Retries a function until the function succeeds or fails the retry strategy. The default retry strategy attempts 5 calls and exponentially increases the delay between each call. You can define your own retry strategy. |
| `CaptureErrors` | Calls a function, capturing all PHP errors raised during the execution of the function. Provides access to the queue of captured errors afterwards. |
| `Expression` | Converts a string containing a PHP expression into a callable. Useful when you want to build small functions without the overhead of PHP closure syntax. |
| `Y` | Implements the [Y-combinator][pack4], which is a way to express a recursive algorithm with neither recursion nor iteration. |

[pack1]: http://haldayne-docs.rtfd.org/
[pack2]: http://haldayne.github.io/documentation/api/
[pack3]: http://php.net/manual/en/language.oop5.magic.php#object.invoke 
[pack4]: http://matt.might.net/articles/implementation-of-recursive-fixed-point-y-combinator-in-javascript-for-memoization/


# Miscellaneous and Trivia

These algorithms can be composed together. The repository name `fox` is an
abbreviation for `f(x)` said "f of x", which harks back to this function
composition theme.

Both [Go][misc1] and [Java][misc2] have implementations of the Retry function
with exponential backoff.

[misc1]: https://github.com/cenkalti/backoff
[misc2]: https://github.com/google/google-http-java-client
