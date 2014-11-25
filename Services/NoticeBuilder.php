<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;

/**
 * Notice builder
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class NoticeBuilder
{
    /**
     * Total
     *
     * @var int
     */
    private $total = 0;

    /**
     * Success
     *
     * @var int
     */
    private $success = 0;

    /**
     * Failed
     *
     * @var int
     */
    private $failed = 0;

    /**
     * Success
     */
    public function addSuccess()
    {
        $this->success++;
        $this->total++;
    }

    /**
     * Fail
     */
    public function addFail()
    {
        $this->failed++;
        $this->total++;
    }

    /**
     * Notices getter
     *
     * @return array
     */
    public function getNotices()
    {
        return array(
            'total' => $this->total,
            'success' => $this->success,
            'failed' => $this->failed
        );
    }

    /**
     * Is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ! (bool) $this->total;
    }

    /**
     * Setting transaction notices
     *
     * @param Transaction $transaction
     */
    public function setTransactionNotices(Transaction &$transaction)
    {
        $messages = $transaction->getMessages();
        if (is_null($messages)) {
            $messages = new ArrayCollection();
        }
        if (!$this->isEmpty()) {
            $messages->set('notices', $this->getNotices());
        }

        if ($messages->count()) {
            $transaction->setMessages($messages);
        }
    }

}