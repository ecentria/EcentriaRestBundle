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

/**
 * Create staus check event
 * This event checks if services work correctly.
 *
 * @author Michael Kuzmyn <michael.kuzmyn@intexsys.lv>
 */
class StatusCheckEvent extends Event
{
    /**
     * List of messages that notify malfunctions of services
     *
     * @var array
     */
    private $messages;

    /**
     * Getter
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add Message
     *
     * @param string $message
     */
    public function addMessages($message)
    {
        $this->messages[] = $message;
    }

    /**
     * Reset the list of messages
     */
    public function resetMessages()
    {
        $this->messages = [];
    }
}
