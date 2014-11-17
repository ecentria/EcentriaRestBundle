<?php
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
use Ecentria\Libraries\CoreRestBundle\Model\Error;
use Ecentria\Libraries\CoreRestBundle\Tests\Entity\CRUDEntity;
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
     * @var ArrayCollection
     */
    private $collectionToInsert;

    /**
     * @var ArrayCollection
     */
    private $collectionToUpdate;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param RecursiveValidator $validator
     * @param EventDispatcherInterface $eventDispatcher
     * @param CRUDTransformer $crudTransformer
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

        $this->collectionToInsert = new ArrayCollection();
        $this->collectionToUpdate = new ArrayCollection();
    }

    /**
     * Mode setter
     *
     * @param string $mode
     *
     * @return self
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
     * @param string $class
     * @param mixed $id
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
     * @param object $entity
     * @param bool $flush
     * @return object
     */
    public function create($entity, $flush = true)
    {
        $this->eventDispatcher->dispatch(
            Events::PRE_CREATE,
            new CRUDEvent($entity)
        );
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush($entity);
        }
        $this->eventDispatcher->dispatch(
            Events::POST_CREATE,
            new CRUDEvent($entity)
        );
        return $entity;
    }

    /**
     * Creating collection
     *
     * @param CRUDEntityInterface[]|ArrayCollection $collection
     * @return void
     */
    public function createCollection(ArrayCollection $collection)
    {
        $this->filterCollection($collection);
        $this->validateCollection($this->collectionToInsert);

        foreach ($this->collectionToInsert as $collectionItem) {
            $this->create($collectionItem, false);
        }
        foreach ($this->collectionToUpdate as $collectionItem) {
            $this->update($collectionItem);
        }

        $this->entityManager->flush();
    }

    /**
     * Entity validation
     *
     * @param object $entity
     * @return ConstraintViolationList|bool
     */
    public function validate($entity)
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
     * @param ArrayCollection|CRUDEntityInterface[] $collection
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
     * @param ArrayCollection|CRUDEntityInterface[] $collection
     * @return bool
     * @throws ValidationFailedException
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
     * @param CRUDEntityInterface $entity
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
     * Updating one entity
     *
     * @param CRUDEntityInterface $entity
     * @param array $data
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
     * @param object $entity
     * @throws ValidationFailedException
     */
    public function save($entity)
    {
        $violations = $this->validate($entity);

        if ($violations instanceof ConstraintViolationList) {
            throw new ValidationFailedException($violations);
        }
        $this->entityManager->flush($entity);
    }

    /**
     * @param CRUDEntityInterface $entity
     * @throws \JMS\Serializer\Exception\ValidationFailedException
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
     * @param CRUDEntityInterface[]|ArrayCollection $collection
     * @return ArrayCollection
     */
    private function filterCollection(ArrayCollection $collection)
    {
        if ($this->mode === self::MODE_DEFAULT) {
            $this->collectionToInsert = $collection;
            return $collection;
        }
        foreach ($collection as $entity) {
            $crudEntity = $this->find(get_class($entity), $entity->getId());
            if ($crudEntity instanceof CRUDEntityInterface) {
                $this->setData($crudEntity, array($entity->toArray()));
                $this->collectionToUpdate->add($crudEntity);
            } else {
                $this->collectionToInsert->add($entity);
            }
        }
        return $collection;
    }
}