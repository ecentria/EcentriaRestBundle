<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2016, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\Storage;

use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction as TransactionModel;
use JMS\Serializer\Serializer;
use Monolog\Logger;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Monolog Transaction Storage
 *
 * @author Artem Petrov <artem.petrov@opticsplanet.com>
 */
class Monolog implements TransactionStorageInterface {

    /**
     * Monolog logger
     *
     * @var Logger
     */
    private $logger;

    /**
     * JMS serializer
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * Persistent transactions
     *
     * @var ArrayCollection
     */
    private $persistentTransactions;

    /**
     * Constructor.
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger, Serializer $serializer) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->persistentTransactions = new ArrayCollection();
    }


    /**
     * {@inheritDoc}
     */
    public function persist(TransactionModel $transaction) {
        if (!$this->persistentTransactions->contains($transaction)) {
            $this->persistentTransactions->add($transaction);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function write()
    {
        /** @var TransactionModel $transaction */
        foreach ($this->persistentTransactions as $transaction) {
            $level = Logger::DEBUG;

            foreach (Logger::getLevels() as $value) {
                if ($value > $transaction->getStatus()) {
                    break;
                }
                $level = $value;
            }
            $errorMessage = '';
            if (isset($transaction->getMessages()['errors'])) {
                foreach ($transaction->getMessages()['errors'] as $error) {
                    if ($errorMessage != '') {
                        $errorMessage .= ', ';
                    }
                    $errorMessage .= $error['message'];
                }
            }
            $this->logger->log(
                $level,
                'Transaction Log' . ($errorMessage == '' ? '' : ': ' . $errorMessage),
                json_decode(
                    $this->serializer->serialize($transaction, 'json'),
                    true
                )
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function read($id)
    {
        throw new \LogicException('Monolog storage interface does not support read operations');
    }
}
