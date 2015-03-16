<?php
// @codingStandardsIgnoreFile
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services\CRUD;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Ecentria\Libraries\CoreRestBundle\Event\CRUDEvent;
use Ecentria\Libraries\CoreRestBundle\Event\Events;
use Ecentria\Libraries\CoreRestBundle\Model\CRUD\CRUDEntityInterface;
use Ecentria\Libraries\CoreRestBundle\Model\CRUD\CRUDUnitOfWork;
use Ecentria\Libraries\CoreRestBundle\Model\Error;
use JMS\Serializer\Exception\ValidationFailedException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * CRUD Manager
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CRUDManager
{
    const MODE_DEFAULT = 'default';
    const MODE_UPDATE_ON_DUPLICATE = 'update_on_duplicate';
    const MODE_DRY_RUN = 'dry_run';

    /**
     * Entity manager
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Validator
     *
     * @var RecursiveValidator
     */
    private $validator;

    /**
     * Event dispatcher
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * CRUDTransformer
     *
     * @var CRUDTransformer
     */
    private $crudTransformer;

    /**
     * Entity handling mode
     *
     * @var string
     */
    private $mode = self::MODE_DEFAULT;

    /**
     * Constructor
     *
     * @param EntityManager            $entityManager   Entity manager
     * @param RecursiveValidator       $validator       Validator
     * @param EventDispatcherInterface $eventDispatcher Event dispatcher
     * @param CRUDTransformer          $crudTransformer Crud transformer
     */
    public function __construct(
        EntityManager $entityManager,
        RecursiveValidator $validator,
        EventDispatcherInterface $eventDispatcher,
        CRUDTransformer $crudTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
        $this->crudTransformer = $crudTransformer;
    }

    /**
     * Mode setter
     *
     * @param string $mode Mode
     *
     * @return CRUDManager
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Mode getter
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Find
     *
     * @param string $class Class
     * @param mixed  $id    Id
     *
     * @return object
     */
    public function find($class, $id)
    {
        return $this->entityManager->find($class, $id);
    }

    /**
     * Creating entity
     *
     * @param CRUDEntityInterface $entity Entity
     * @param bool                $flush  Flush
     *
     * @return object
     */
    public function create(CRUDEntityInterface $entity, $flush = true)
    {
        $this->eventDispatcher->dispatch(
            Events::PRE_CREATE,
            new CRUDEvent($entity)
        );
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->flush($entity);
        }
        $this->eventDispatcher->dispatch(
            Events::POST_CREATE,
            new CRUDEvent($entity)
        );
        return $entity;
    }

    /**
     * Flush
     *
     * @param CRUDEntityInterface $entity Entity
     *
     * @return void
     */
    private function flush(CRUDEntityInterface $entity = null)
    {
        if ($this->getMode() !== self::MODE_DRY_RUN) {
            $this->entityManager->flush($entity);
        }
    }

    /**
     * Creating collection
     *
     * @param ArrayCollection|CRUDEntityInterface[] $collection Collection
     * @return void
     */
    public function createCollection(ArrayCollection $collection)
    {
        $this->validateCollection($collection);
        foreach ($collection as $collectionItem) {
            $this->create($collectionItem, false);
        }
        $this->flush();
    }

    /**
     * Entity validation
     *
     * @param CRUDEntityInterface $entity Entity
     * @return ConstraintViolationList|bool
     */
    public function validate(CRUDEntityInterface $entity)
    {
        $violations = $this->validator->validate($entity);
        if ($violations->count()) {
            return $violations;
        }
        return true;
    }

    /**
     * Validate collection uniqueness
     *
     * @param ArrayCollection|CRUDEntityInterface[] $collection Collection
     * @return ConstraintViolationList
     */
    private function validateCollectionUniqueness(ArrayCollection $collection)
    {
        $constraintViolationList = new ConstraintViolationList();
        foreach ($collection as $collectionItem) {
            $count = 0;
            foreach ($collection as $collectionItemToCompare) {
                if ($collectionItemToCompare->getId() == $collectionItem->getId() && $collectionItem->getId()) {
                    $count++;
                }
            }
            if ($count > 1) {
                // TODO: move this to different class
                $violation = new ConstraintViolation(
                    'Collection contains duplicate entities',
                    'Collection contains duplicate entities',
                    array(
                        'context' => Error::CONTEXT_GLOBAL
                    ),
                    $collectionItem,
                    'id',
                    '',
                    null,
                    409
                );
                $constraintViolationList->add($violation);
                return $constraintViolationList;
            }
        }
        return $constraintViolationList;
    }

    /**
     * Collection validation
     *
     * @param ArrayCollection|CRUDEntityInterface[] $collection collection
     * @throws ValidationFailedException
     * @return bool
     */
    public function validateCollection(ArrayCollection $collection)
    {
        $violations = $this->validateCollectionUniqueness($collection);
        foreach ($collection as $collectionItem) {
            $itemViolations = $this->validate($collectionItem);
            if ($itemViolations instanceof ConstraintViolationList) {
                $violations->addAll($itemViolations);
            }
        }
        if ($violations->count()) {
            throw new ValidationFailedException($violations);
        }
        return true;
    }

    /**
     * Update entity
     *
     * @param CRUDEntityInterface $entity entity
     * @return void
     */
    public function update(CRUDEntityInterface $entity)
    {
        $this->save($entity);
        $this->eventDispatcher->dispatch(
            Events::POST_UPDATE,
            new CRUDEvent($entity)
        );
    }

    /**
     * Creating collection
     *
     * @param ArrayCollection|CRUDEntityInterface[] $collection collection
     * @return void
     */
    public function updateCollection(ArrayCollection $collection)
    {
        foreach ($collection as $entity) {
            $this->update($entity);
        }
    }

    /**
     * Updating one entity
     *
     * @param CRUDEntityInterface $entity Entity
     * @param array               $data   Data
     *
     * @throws \Exception
     * @return void
     */
    public function setData(CRUDEntityInterface $entity, array $data = array())
    {
        $data = reset($data);
        $this->validateExistence($entity);
        $this->crudTransformer->initializeClassMetadata(get_class($entity));
        foreach ($data as $property => $value) {
            $this->crudTransformer->processPropertyValue($entity, $property, $value, 'update');
        }
    }

    /**
     * Saving
     *
     * @param CRUDEntityInterface $entity Entity
     * @throws ValidationFailedException
     * @return void
     */
    public function save(CRUDEntityInterface $entity)
    {
        $violations = $this->validate($entity);

        if ($violations instanceof ConstraintViolationList) {
            throw new ValidationFailedException($violations);
        }
        $this->flush($entity);
    }

    /**
     * Validates existence
     *
     * @param CRUDEntityInterface $entity Entity
     * @throws ValidationFailedException
     *
     * @return void
     */
    public function validateExistence(CRUDEntityInterface $entity)
    {
        if (UnitOfWork::STATE_MANAGED !== $this->entityManager->getUnitOfWork()->getEntityState($entity)) {
            // TODO: move this to different class
            $violation = new ConstraintViolation(
                'Entity not found',
                'Entity not found',
                array(
                    'context' => Error::CONTEXT_GLOBAL
                ),
                $entity,
                'id',
                $entity->getId(),
                null,
                404
            );
            $violations = new ConstraintViolationList(array($violation));
            throw new ValidationFailedException($violations);
        }
    }

    /**
     * Filtering collection
     *
     * @param ArrayCollection|CRUDEntityInterface[] $collection Collection
     *
     * @return CRUDUnitOfWork
     */
    public function filterCollection(ArrayCollection $collection)
    {
        $unitOfWork = new CRUDUnitOfWork();
        foreach ($collection as $entity) {
            $crudEntity = $this->find(get_class($entity), $entity->getId());
            if ($crudEntity instanceof CRUDEntityInterface) {
                $this->setData($crudEntity, array($entity->toArray()));
                $unitOfWork->update($crudEntity);
            } else {
                $unitOfWork->insert($entity);
            }
        }
        return $unitOfWork;
    }

    /**
     * Processing unit of work
     *
     * TODO: refactor api to use unit of work always
     *
     * @param CRUDUnitOfWork $unitOfWork UnitOfWork
     * @return void
     */
    public function processUnitOfWork(CRUDUnitOfWork $unitOfWork)
    {
        if ($unitOfWork->getInsertions()->count()) {
            $this->createCollection($unitOfWork->getInsertions());
        }
        if ($unitOfWork->getUpdates()->count()) {
            $this->updateCollection($unitOfWork->getUpdates());
        }
    }
}
