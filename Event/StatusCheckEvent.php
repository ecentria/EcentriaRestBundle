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

    CONST STATE_OK_CODE      = 0;
    CONST STATE_WARNING_CODE = 1;
    CONST STATE_FAILURE_CODE = 2;

    /**
     * List of available states
     *
     * States should be ordered from normal (lower level) to critical (higher level).
     * Array's keys are used to check if a new state is not downgraded in 'setState' method.
     *
     * @var array
     */
    private static $availableStates = [
        self::STATE_OK_CODE      => self::STATE_OK,
        self::STATE_WARNING_CODE => self::STATE_WARNING,
        self::STATE_FAILURE_CODE => self::STATE_FAILURE
    ];

    /**
     * State code
     * (0 => 'Ok', 1 => 'Warning', 2 => 'Failure')
     *
     * @var string
     */
    private $stateCode;

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
        $this->stateCode  = key(self::$availableStates);
        $this->exceptions = [];
    }

    /**
     * Gets state
     *
     * @return string
     */
    public function getState()
    {
        return self::$availableStates[$this->stateCode];
    }

    /**
     * Gets code of the status state
     *
     * @return string
     */
    public function getStateCode()
    {
        return $this->stateCode;
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
        if (($stateCode = array_search($state, self::$availableStates)) === false) {
            throw new \InvalidArgumentException("State $state is not in the list of available states");
        }
        // State can be escalated only. State downgrading should be prohibited.
        if ($this->isStateEscalated($stateCode)) {
            $this->stateCode = $stateCode;
        }
        return $this;
    }

    /**
     * Checks if new state can be downgraded from 'Error' to 'Warning', from 'Warning' to 'Ok', etc.
     * Keys of self::$availableStates indicate grades of status states
     *
     * @param int $newStateCode
     * @return bool
     */
    private function isStateEscalated($newStateCode)
    {
        if ($newStateCode < $this->stateCode) {
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
                return ['Message' => $e->getMessage(), 'Trace' => $this->makePrettyTrace($e)];
            },
            $this->exceptions
        );
    }

    /**
     * Formats stack trace for output.
     *
     * One of the method's goal is to remove function arguments from the output
     * as function arguments can contain sensitive information such as usernames and passwords
     *
     * @param \Exception $e
     * @return array
     */
    private function makePrettyTrace(\Exception $e)
    {
        $output = [];
        $trace = $e->getTrace();
        foreach ($trace as $stackFrame) {
            unset($stackFrame['args']);
            $output[] = $stackFrame;
        }
        return $output;
    }
}
