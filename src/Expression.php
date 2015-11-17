<?php
namespace Haldayne\Fox;

/**
 * Manufactures a callable from a string.
 *
 * In PHP, a callable can be a function name (as a string), a class or object
 * method specification (as an array [object|string, string], an anonymous
 * function (via create_function), or a closure (function () { ... }). Sadly,
 * the syntax for these callable methods may dwarf the meat of the code to
 * run:
 *
 * ```
 * uasort($array, function ($a, $b) { return $a <=> $b; });
 * // vs. an ideal expression
 * uasort($array, '$_0 <=> $_1');
 * ```
 *
 * This class wraps anonymous functions around string code "expressions", in a
 * reasonably performant manner. Libraries wanting to support concise
 * expressions as arguments to their functions can then use this class to
 * produce that effect:
 *
 * ```
 * use Haldayne\Fox\Expression;
 * function filter(array $array, $expression) {
 *     return array_filter($array, new Expression($expression));
 * }
 * print_r(filter([ 'bee', 'bear', 'goose' ], '4 <= strlen($_0)'));
 * ```
 *
 * @see http://php.net/manual/en/language.types.callable.php
 * @see https://linepogl.wordpress.com/2011/07/09/on-the-syntax-of-closures-in-php/
 * @see http://justafewlines.com/2009/10/whats-wrong-with-php-closures/
 * @see https://wiki.php.net/rfc/short_closures
 * @see http://docs.hhvm.com/manual/en/hack.lambda.php
 * @see https://linepogl.wordpress.com/2011/08/04/short-closures-for-php-an-implementation/
 */
class Expression
{
    /**
     * Creates a callable returning the given expression.
     *
     * If the expression is already callable, returns it untouched. If the
     * expression is a string, a closure will be wrapped around the string
     * returning it as a single value. In this case, the first 10 positional
     * arguments are available as $_0, $_1, ..., $_9.
     *
     * ```
     * $lt = new Expression('$_0 < $_1'); // expressions is a comparison
     * var_dump($lt(0, 1)); // true
     * var_dump($lt(1, 0)); // false
     * var_dump($lt());     // false (null not less than null)
     * var_dump($lt(-1));   // true (-1 is less than null)
     * ```
     *
     * Do not include a `return` in your expression.
     * 
     * @param callable|string $expression
     * @throws \InvalidArgumentException When $expression not of expected type
     * @throws \LogicException When $expression does not form valid PHP code
     *
     * @see http://php.net/manual/en/language.expressions.php Definition of PHP expression
     */
    public function __construct($expression)
    {
        if (is_callable($expression)) {
            $this->callable = $expression;
        
        } else if (is_string($expression)) {
            $this->callable = static::makeCallable($expression);

        } else {
            throw new \InvalidArgumentException(sprintf(
                'Argument $expression (of type %s) must callable or string',
                gettype($expression)
            ));
        }
    }

    /**
     * Convenience method to execute the the manufactured callable from the
     * object itself.
     *
     * @param mixed ...args Up to 10 arguments passed into the built callable
     * @return mixed 
     */
    public function __invoke()
    {
        return call_user_func_array($this->callable, func_get_args());
    }

    /**
     * Get the callable manufactured for this expression.
     *
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    // PRIVATE API

    /**
     * Manufactured callables all have this formal signature, which allows up
     * to 10 parameters to be passed in, accessible as $_N, where 0 <= N <= 9.
     * @var $signature
     */
    private static $signature = '$_0=null, $_1=null, $_2=null, $_3=null, $_4=null,$_5=null, $_6=null, $_7=null, $_8=null, $_9=null';

    /**
     * In memory cache of built expressions.
     * @var \Haldayne\Boost\Map $map
     */
    private static $map = [];

    /**
     * Given a string expression, turn that into an anonymous function.
     * Cache the result, so as to keep memory 
     */
    private static function makeCallable($expression)
    {
        if (! array_key_exists($expression, static::$map)) {
            $return = "return ($expression);";
            $lambda = create_function(static::$signature, $return);
            if (false === $lambda) {
                throw new \LogicException(sprintf(
                    'Expression does not result in valid PHP code. You gave=[%s], becomes=[%s]',
                    $expression,
                    $return
                ));
            } else {
                static::$map[$expression] = $lambda;
            }
        }
        return static::$map[$expression];
    }
}
