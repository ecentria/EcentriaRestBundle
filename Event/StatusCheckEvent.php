<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Ecentria\Libraries\EcentriaRestBundle\Validator\Constraints as EcentriaAssert;

/**
 * Create staus check event
 * This event checks if services work correctly.
 *
 * @author Michael Kuzmyn <michael.kuzmyn@intexsys.lv>
 */
class StatusCheckEvent extends Event
{
    CONST STATE_OK      = 'Ok';
    CONST STATE_WARNING = 'Warning';
    CONST STATE_FAILURE = 'Failure';

    /**
     * List of available states
     * States should be ordered from normal (lower level) to critical (higher level).
     * As setState method should only allow escalating the state and not downgrading it,
     * the array's key is used to check if a new state is not downgraded.
     *
     * @var array
     */
    private static $availableStates = [self::STATE_OK, self::STATE_WARNING, self::STATE_FAILURE];

    /**
     * state
     * ('Ok', 'Warning', 'Failure')
     *
     * @var string
     */
    private $state;

    /**
     * List of messages that notify malfunctions of services
     *
     * @var array
     */
    private $exceptions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->state     = self::STATE_OK;
        $this->exceptions = [];
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getstate()
    {
        return $this->state;
    }

    /**
     * Setter
     *
     * @param string $state
     * @return StatusCheckEvent
     *
     * @throws \InvalidArgumentException
     */
    public function setState($state)
    {
        if (!in_array($state, self::$availableStates)) {
            throw new \InvalidArgumentException("State $state is not in the list of available states");
        }
        // State can be escalated only. State downgrading should be prohibited.
        if ($this->isStateEscalated($state)) {
            $this->state = $state;
        }
        return $this;
    }

    /**
     * Checks if new state can be downgraded from 'Error' to 'Warning', from 'Warning' to 'Ok', etc.
     *
     * @param string $newState
     * @return bool
     */
    private function isStateEscalated($newState)
    {
        if (array_search($newState, self::$availableStates) < array_search($this->state, self::$availableStates)) {
            return false;
        }
        return true;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * Add Exception
     *
     * @param \Exception $exception
     * @return StatusCheckEvent
     */
    public function addException($exception)
    {
        $this->exceptions[] = $exception;
        return $this;
    }

    /**
     * Reset messages
     *
     * @return StatusCheckEvent
     */
    public function resetExceptions()
    {
        $this->exceptions = [];
        return $this;
    }

    /**
     * Get array of codes of exception thrown during status checks
     *
     * @return array
     */
    public function getExceptionCodes()
    {
        return array_map(
            function (\Exception $e) {
                return $e->getCode();
            },
            $this->exceptions
        );
    }

    /**
     * Get array of codes of exception thrown during status checks
     *
     * @return array
     */
    public function getExceptionMessages()
    {
        return array_map(
            function (\Exception $e) {
                return ['Message' => $e->getMessage(), 'Stack Trace' => $e->getTraceAsString()];
            },
            $this->exceptions
        );
    }
}
