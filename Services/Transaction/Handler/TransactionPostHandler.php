<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\Handler;

use Doctrine\Common\Collections\ArrayCollection;

use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction,
    Ecentria\Libraries\EcentriaRestBundle\Model\CollectionResponse,
    Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\EcentriaRestBundle\Services\ErrorBuilder,
    Ecentria\Libraries\EcentriaRestBundle\Services\NoticeBuilder,
    Ecentria\Libraries\EcentriaRestBundle\Services\UUID,
    Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\Storage\TransactionStorageInterface;

use Ecentria\Libraries\EcentriaRestBundle\Services\InfoBuilder;
use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction POST handler
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionPostHandler implements TransactionHandlerInterface
{
    /**
     * Info builder
     *
     * @var InfoBuilder
     */
    protected $infoBuilder;

    /**
     * Error builder
     *
     * @var ErrorBuilder
     */
    protected $errorBuilder;

    /**
     * Notice builder
     *
     * @var NoticeBuilder
     */
    protected $noticeBuilder;

    /**
     * Transaction Storage
     *
     * @var TransactionStorageInterface
     */
    protected $transactionStorage;

    /**
     * Constructor
     *
     * @param TransactionStorageInterface $transactionStorage Transaction Storage
     * @param ErrorBuilder                $errorBuilder       errorBuilder
     * @param NoticeBuilder               $noticeBuilder      noticeBuilder
     * @param InfoBuilder                 $infoBuilder        infoBuilder
     */
    public function __construct(
        TransactionStorageInterface $transactionStorage,
        ErrorBuilder $errorBuilder,
        NoticeBuilder $noticeBuilder,
        InfoBuilder $infoBuilder
    ) {
        $this->transactionStorage = $transactionStorage;
        $this->errorBuilder = $errorBuilder;
        $this->noticeBuilder = $noticeBuilder;
        $this->infoBuilder = $infoBuilder;
    }

    /**
     * Supports method
     *
     * @return string
     */
    public function supports()
    {
        return 'POST';
    }

    /**
     * Handle
     *
     * @param Transaction                         $transaction Transaction
     * @param CrudEntityInterface|ArrayCollection $data        Data
     * @param ConstraintViolationList|null        $violations  Violations
     *
     * @throws FeatureNotImplementedException
     *
     * @return CrudEntityInterface|CollectionResponse
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        $this->errorBuilder->processViolations($violations);
        $this->errorBuilder->setTransactionErrors($transaction);

        $success = !$this->errorBuilder->hasErrors();
        $status = $success ? Transaction::STATUS_CREATED : Transaction::STATUS_CONFLICT;

        $transaction->setStatus($status);
        $transaction->setSuccess($success);

        $transaction->setRelatedIds(null);

        if ($data instanceof ArrayCollection) {
            if ($data->isEmpty()) {
                $data = $this->handleEmptyCollection($transaction, $data);
            } else {
                $data = $this->handleCollection($transaction, $data);
            }
        } elseif ($data instanceof CrudEntityInterface) {
            $this->handleEntity($transaction, $data);
        } else {
            throw new FeatureNotImplementedException(
                get_class($data) . ' class is not supported by transactions (POST). Instance of ArrayCollection needed.'
            );
        }

        if (!$transaction->getSuccess()) {
            if ($data instanceof ArrayCollection) {
                $data->clear();
            } else {
                $data = new CollectionResponse(new ArrayCollection(array()));
            }
        }

        return $data;
    }

    /**
     * Handle collection
     *
     * @param Transaction                           $baseTransaction Base transaction
     * @param ArrayCollection|CrudEntityInterface[] $data            Data
     *
     * @return ArrayCollection|CollectionResponse
     */
    private function handleCollection(Transaction $baseTransaction, ArrayCollection $data)
    {
        foreach ($data as $entity) {
            if ($entity instanceof CrudEntityInterface) {
                $transaction = clone $baseTransaction;
                $this->handleEntity($transaction, $entity);
            }
        }

        $this->noticeBuilder->setTransactionNotices($baseTransaction);
        $data = new CollectionResponse($data);

        $this->infoBuilder->setTransactionMessages($baseTransaction);

        return $data;
    }

    /**
     * Handle individual entity
     *
     * @param Transaction         $transaction Transaction
     * @param CrudEntityInterface $entity      Entity
     *
     * @return void
     */
    private function handleEntity($transaction, CrudEntityInterface $entity)
    {
        $transaction->setRequestSource(Transaction::SOURCE_SERVICE);
        $transaction->setId(UUID::generate());
        $transaction->setRequestId(microtime());
        $transaction->setRelatedIds($entity->getIds());


        $errors = $this->errorBuilder->getEntityErrors($entity->getPrimaryKey());
        $messages = new ArrayCollection();

        $success = $errors->isEmpty();
        $status = $success ? Transaction::STATUS_CREATED : Transaction::STATUS_CONFLICT;

        $transaction->setStatus($status);
        $transaction->setSuccess($success);

        if ($success) {
            $this->noticeBuilder->addSuccess();
        } else {
            $messages->set('errors', $errors);
            $this->noticeBuilder->addFail();
        }

        $transaction->setMessages($messages);
        $entity->setTransaction($transaction);

        $this->transactionStorage->persist($transaction);
    }

    /**
     * Handle empty collection
     *
     * @param Transaction                           $baseTransaction Base transaction
     * @param ArrayCollection|CrudEntityInterface[] $data            Data
     *
     * @return ArrayCollection|CollectionResponse
     */
    private function handleEmptyCollection(Transaction $baseTransaction, ArrayCollection $data)
    {
        $this->noticeBuilder->setTransactionNotices($baseTransaction);
        $data = new CollectionResponse($data);

        $baseTransaction->setSuccess(false);
        $baseTransaction->setStatus(Transaction::STATUS_CONFLICT);

        return $data;
    }
}
