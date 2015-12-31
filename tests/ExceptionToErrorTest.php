<?php
namespace Haldayne\Fox;

class ExceptionToErrorTest extends \PHPUnit_Framework_TestCase
{
    public function test_converts_to_error_with_defaults()
    {
        $ex = new \Exception('Boom!', 242);

        $capture = new CaptureErrors(
            $convert = new ExceptionToError(
                function () use ($ex) { throw $ex; return true; }
            )
        );
        $result = $capture();
        $this->assertSame(false, $result);
        $this->assertSame(
            $convert->formatException($ex),
            $capture->getCapturedErrors()->pop()->get('message')
        );
    }

    public function test_converts_with_customs()
    {
        $ex = new \Exception('Boom!', 242);

        // set up the conversion
        $convert = new ExceptionToError(
            function () use ($ex) { throw $ex; return true; }
        );
        $convert->setErrorMessageFormat('xxx');
        $convert->setErrorCode(E_USER_NOTICE);

        // capture
        $capture = new CaptureErrors($convert);
        $result = $capture();

        // ensure we match on the custom values
        $this->assertSame(
            'xxx',
            $capture->getCapturedErrors()->get(0)->get('message')
        );
        $this->assertSame(
            E_USER_NOTICE,
            $capture->getCapturedErrors()->get(0)->get('code')
        );
    }
}
