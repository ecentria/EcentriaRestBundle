<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services\Transaction\Handler;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction,
    Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse,
    Ecentria\Libraries\CoreRestBundle\Model\CRUD\CRUDEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Services\ErrorBuilder,
    Ecentria\Libraries\CoreRestBundle\Services\NoticeBuilder,
    Ecentria\Libraries\CoreRestBundle\Services\UUID;

use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction service
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionPostHandler implements TransactionHandlerInterface
{
    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param ErrorBuilder $errorBuilder
     * @param NoticeBuilder $noticeBuilder
     */
    public function __construct(
        EntityManager $entityManager,
        ErrorBuilder $errorBuilder,
        NoticeBuilder $noticeBuilder
    ) {
        $this->entityManager = $entityManager;
        $this->errorBuilder = $errorBuilder;
        $this->noticeBuilder = $noticeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function supports()
    {
        return 'POST';
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        $this->errorBuilder->processViolations($violations);
        $this->errorBuilder->setTransactionErrors($transaction);

        $success = !$this->errorBuilder->hasErrors();
        $status = $success ? Transaction::STATUS_CREATED : Transaction::STATUS_CONFLICT;

        $transaction->setStatus($status);
        $transaction->setSuccess($success);

        if ($data instanceof ArrayCollection) {
            $data = $this->handleCollection($transaction, $data);
        } else {
            throw new FeatureNotImplementedException(
                get_class($data) . ' class is not supported by transactions (POST). Instance of ArrayCollection needed.'
            );
        }

        return $data;
    }

    /**
     * Handle collection
     *
     * @param Transaction $baseTransaction
     * @param ArrayCollection|CRUDEntityInterface[] $data
     *
     * @return ArrayCollection|CollectionResponse
     */
    private function handleCollection(Transaction $baseTransaction, ArrayCollection $data)
    {
        foreach ($data as $entity) {

            $transaction = clone $baseTransaction;

            $transaction->setRequestSource(Transaction::SOURCE_SERVICE);
            $transaction->setId(UUID::generate());
            $transaction->setRequestId(microtime());
            $transaction->setRelatedId($entity->getId());

            $errors = $this->errorBuilder->getEntityErrors($entity->getId());
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
            $this->entityManager->persist($transaction);
            $entity->setTransaction($transaction);
        }

        $this->noticeBuilder->setTransactionNotices($baseTransaction);
        $data = new CollectionResponse($data);
        $data->setShowAssociations(true);

        return $data;
    }
}