<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2015, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction;


/**
 * InfoBuilder
 *
 * @author Arturs Reiljans <artur.reiljans@intexsys.lv>
 */
class InfoBuilder
{
    /**
     * Messages
     *
     * @var ArrayCollection
     */
    private $messages;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    /**
     * Add message
     *
     * @param string $key     Key
     * @param string $message Message
     * @return $this
     */
    public function addMessage($key, $message)
    {
        $this->messages->set($key, $message);
        return $this;
    }

    /**
     * Set transaction messages
     *
     * @param Transaction $transaction Transaction
     * @return $this
     */
    public function setTransactionMessages(Transaction $transaction)
    {
        $messages = $transaction->getMessages();
        $messages->set('info', $this->messages);
        $transaction->setMessages($messages);
        return $this;
    }
}
