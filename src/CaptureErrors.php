<?php
namespace Haldayne\Fox;

use Haldayne\Boost\Map;
use Haldayne\Boost\MapOfCollections;

/**
 * Runs a callable, capturing all error messages, then provides a method to
 * extract any errors raised during execution.
 *
 * ```
 * use Haldayne\Fox\CaptureErrors;
 * $helper = new CaptureErrors(function ($src, $dst) { return copy($src, $dst); });
 * if (false === $helper('foo', 'bar')) {
 *     throw new \RuntimeException($helper->getCapturedErrors()->pop()->get('message'));
 * }
 * ```
 *
 * PHP itself, as well as many libraries, rely upon triggered errors to notify
 * developers of problems. Many frameworks provide methods to capure these
 * errors globally and convert them to exceptions, but you do not always have
 * access to those frameworks.  Worse, you may be using code that deliberately
 * silences errors.
 *
 * Errors raised inside the CaptureErrors helper is pushed into a Map stack,
 * with each element also a Map of the error details: error code, error
 * string, file and line where raised, and an array context of variables
 * set at time of error.
 */
class CaptureErrors
{
    /**
     * Create a new callable object that captures errors when the given code
     * is invoked. By default, all errors (E_ALL) are captured. You can set
     * which errors will be captured using the `setCapturedErrorTypes` method.
     *
     * @param callable $code
     */
    public function __construct(callable $code)
    {
        $this->code = $code;
        $this->capturedErrorTypes = E_ALL;
        $this->map = new MapOfCollections();
    }

    /**
     * Set the error types to capture. Acts as a filter: only those errors
     * matching this value will be captured.
     *
     * @param int $capturedErrorTypes One or more of the E_* error constants
     * @return void
     * @throws \InvalidArgumentException If $capturedErrorTypes isn't an int
     */
    public function setCapturedErrorTypes($capturedErrorTypes)
    {
        if (is_int($capturedErrorTypes)) {
            $this->capturedErrorTypes = $capturedErrorTypes;
        } else {
            throw new \InvalidArgumentException('$capturedErrorTypes must be integer');
        }
    }

    /**
     * Invoke the helper. Passes any arguments into the callable given during
     * helper construction. Returns any result from the callable.
     */
    public function __invoke(/* ... args */)
    {
        $old_handler = set_error_handler(
            function ($code, $message, $file, $line, $context) {
                $this->map->push(
                    new Map(compact('code', 'message', 'file', 'line', 'context'))
                );
            },
            $this->capturedErrorTypes
        );
        $result = call_user_func_array($this->code, func_get_args());
        set_error_handler($old_handler);
        return $result;
    }

    /**
     * Get the captured errors.
     *
     * @return \Haldayne\Boost\MapOfCollections
     */
    public function getCapturedErrors()
    {
        return $this->map;
    }

    // PRIVATE API

    private $code = null;
    private $capturedErrorTypes = null;
    private $map = null;
}
