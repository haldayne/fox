<?php
namespace Haldayne\Fox;

use Haldayne\Boost\Map;
use Haldayne\Boost\MapOfCollections;

/**
 * Runs a callable, catching any exception and reflecting it into a triggered
 * error.
 *
 * This allows you to combine exception generating code with retry code to
 * trace the execution over the retry cycle. Example:
 *
 * ```
 * $retry = new \Haldayne\Fox\Retry(
 *      $capture = new \Haldayne\Fox\CaptureErrors(
 *          new \Haldayne\Fox\ExceptionToError(
 *              function () { throw new \Exception('Oops!', rand(1,10)); }
 *          )
 *      )
 * );
 * $result = $retry();
 * if (false === $result) {
 *     die(
 *         $capture->
 *         getCapturedErrors()->
 *         pluck('message')->
 *         into(new MapOfStrings)->
 *         join(PHP_EOL)
 *     );
 * }
 */
class ExceptionToError
{
    /**
     * Create a new callable object that captures any exception when the given
     * callable is invoked.
     *
     * @param callable $code
     */
    public function __construct(callable $code)
    {
        $this->code = $code;
    }

    /**
     * Change the format of the generated error messages. Accepts a printf-
     * style format string, where the following parameters are replaced:
     * - %1$s The exception message
     * - %2$d The exception code
     * - %3$s The file where the exception occurred
     * - %4$d The line where the exception occurred
     * - %5$s The class of the raised exception
     *
     * The default format is:
     * %1$s (%2$d) thrown at %3$s:%4$d as %5$s exception.
     */
    public function setErrorMessageFormat($format)
    {
        $this->errorMessageFormat = $format;
    }

    /**
     * Gets the current error message format.
     */
    public function getErrorMessageFormat()
    {
        return $this->errorMessageFormat;
    }

    /**
     * Change the code used to deliver the exceptions. By default, exceptions
     * converted to E_USER_ERROR errors.
     */
    public function setErrorCode($code)
    {
        $this->errorCode = $code;
    }

    /**
     * Gets the current error code.
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Pass the given exception into the error message format string.
     */
    public function formatException(\Exception $ex)
    {
        return sprintf(
            $this->errorMessageFormat,
            $ex->getMessage(),
            $ex->getCode(),
            $ex->getFile(),
            $ex->getLine(),
            get_class($ex)
        );
    }

    /**
     * Invoke the helper. Passes any arguments into the callable given during
     * helper construction. Returns any result from the callable, unless the
     * callable raises an exception: in which case, returns false.
     */
    public function __invoke(/* ... args */)
    {
        try {
            return call_user_func_array($this->code, func_get_args());

        } catch (\Exception $ex) {
            trigger_error($this->formatException($ex), $this->errorCode);
            return false;
        }
    }

    // PRIVATE API

    private $code = null;
    private $errorMessageFormat = '%1$s (%2$d) thrown at %3$s:%4$d as %5$s exception.';
    private $errorCode = E_USER_ERROR;
}
