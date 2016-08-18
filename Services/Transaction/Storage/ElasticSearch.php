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
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Serializer;

/**
 * Monolog Transaction Storage
 *
 * @author Artem Petrov <artem.petrov@opticsplanet.com>
 */
class ElasticSearch implements TransactionStorageInterface {

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
    public function __construct(Serializer $serializer) {
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

    }

    /**
     * {@inheritDoc}
     */
    public function read($id)
    {

    }

}
