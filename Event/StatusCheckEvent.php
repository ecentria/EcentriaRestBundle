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
    CONST STATUS_OK      = 'Ok';
    CONST STATUS_WARNING = 'Warning';
    CONST STATUS_FAILURE = 'Failure';

    /**
     * List of available statuses
     *
     * @var array
     */
    private static $availableStatuses = [self::STATUS_OK, self::STATUS_WARNING, self::STATUS_FAILURE];

    /**
     * Status
     * ('Ok', 'Warning', 'Failure')
     *
     * @var string
     */
    private $status;

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
        $this->status     = self::STATUS_OK;
        $this->exceptions = [];
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Setter
     *
     * @param string $status
     * @return StatusCheckEvent
     *
     * @throws \InvalidArgumentException
     */
    public function setStatus($status)
    {
        if (!in_array($status, self::$availableStatuses)) {
            throw new \InvalidArgumentException("Status $status is not in the list of available statuses");
        }
        $this->status = $status;
        return $this;
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
