<?php
namespace Haldayne\Fox;

class ImproveTest extends \PHPUnit_Framework_TestCase
{
    public function test_iterates_toward_goal()
    {
        $improve = new Improve(
            function ($guess) { return 4 <= $guess; },
            function ($guess) { return $guess + 1; }
        );

        $answer = $improve(1);

        $this->assertSame(4, $answer);
        $this->assertSame([ 1, 2, 3, 4 ], $improve->getGuessHistory()->toArray());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_avoids_runaway_iteration()
    {
        $improve = new Improve(
            function ($guess) { return false; },
            function ($guess) { return $guess; }
        );

        $improve->setMaximumIterations(10);

        $answer = $improve(1);
    }
}
