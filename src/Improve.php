<?php
namespace Haldayne\Fox;

use Haldayne\Boost\Map;

/**
 * Start with a guess, then repeatedly update the guess until we're close
 * enough to the desired solution.
 *
 * ```
 * use Haldayne\Fox\Improve;
 * $calculator = new Improve(
 *     function ($guess) { return abs(($guess * $guess) - ($guess + 1)) < .00001; },
 *     function ($guess) { return 1/$guess + 1; }
 * );
 * $phi = $calculator(1);
 * $calculator->getGuessHistory();
 * ```
 */
class Improve
{
    /**
     * Create a new iterative improvement algorithm.
     *
     * @param callable $decide Decides if the guess is good enough. Receives
     * one argument, the current guess.
     * @param callable $update Updates the guess to a new guess. Receives two
     * arguments: the current guess and the current loop count, in that order.
     */
    public function __construct(callable $decide, callable $update)
    {
        $this->guesses = new Map;
        $this->decide = $decide;
        $this->update = $update;
    }

    /**
     * Set the maximum number of times the iteration will run. Useful when
     * your improvement algorithm is subject to failure cases (like the
     * Newton-Raphson Method).
     *
     * By default, there is no limit.
     *
     * @param int|null $maxIterations
     * @return void
     */
    public function setMaximumIterations($maxIterations)
    {
        if (null === $maxIterations) {
            $this->maxIterations = null;
        } else if (is_numeric($maxIterations) && 0 < intval($maxIterations)) {
            $this->maxIterations = intval($maxIterations);
        } else {
            throw new \InvalidArgumentException(
                '$maxIterations must be null or integer number of iterations greater than 0'
            );
        }
    }

    /**
     * Given an initial guess, decide if the guess is close enough. If not,
     * update the guess to a new value and repeat up to the maximum number
     * of times.
     *
     * @param mixed $guess The initial guess
     * @return mixed The calculated result
     */
    public function __invoke($guess)
    {
        $looped = 0;
        $this->guesses->push($guess);
        while (! call_user_func($this->decide, $guess)) {
            $guess = call_user_func($this->update, $guess);
            $this->guesses->push($guess);

            if (null !== $this->maxIterations && $this->maxIterations <= ++$looped) {
                throw new \RuntimeException(
                    "Reached the iteration limit of {$this->maxIterations}"
                );
            }
        }
        return $guess;
    }

    /**
     * Get the history of guesses, from oldest to youngest.
     *
     * @return \Haldayne\Boost\Map
     */
    public function getGuessHistory()
    {
        return $this->guesses;
    }

    // PRIVATE API

    private $guesses = null;
    private $update = null;
    private $decide = null;
    private $maxIterations = null;
}
