<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services\CRUD;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Ecentria\Libraries\EcentriaRestBundle\Event\CrudCollectionEvent;
use Ecentria\Libraries\EcentriaRestBundle\Event\CrudEvent;
use Ecentria\Libraries\EcentriaRestBundle\Event\Events;
use Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudEntityInterface;
use Ecentria\Libraries\EcentriaRestBundle\Model\Validatable\ValidatableInterface;
use Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudUnitOfWork;
use Ecentria\Libraries\EcentriaRestBundle\Model\Error;
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
class CrudManager
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
     * CrudTransformer
     *
     * @var CrudTransformer
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
     * @param CrudTransformer          $crudTransformer Crud transformer
     */
    public function __construct(
        EntityManager $entityManager,
        RecursiveValidator $validator,
        EventDispatcherInterface $eventDispatcher,
        CrudTransformer $crudTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
        $this->crudTransformer = $crudTransformer;
    }

    /**
     * Refresh entity
     *
     * @param CrudEntityInterface &$entity entity
     * @param mixed               $id      id
     *
     * @return CrudEntityInterface
     */
    public function refresh(CrudEntityInterface &$entity, $id = null)
    {
        $result = null;
        if ($id) {
            $result = $this->entityManager->find(get_class($entity), $id);
        } else {
            $this->crudTransformer->initializeClassMetadata(get_class($entity));
            $conditions = $this->crudTransformer->getUniqueSearchConditions($entity);
            if (!empty($conditions)) {
                $result = $this->entityManager->getRepository(get_class($entity))->findOneBy($conditions);
            }
        }

        if ($result instanceof $entity) {
            $entity = $result;
        }

        return $entity;
    }

    /**
     * Refresh collection
     *
     * @param ArrayCollection $collection collection
     *
     * @return ArrayCollection
     */
    public function refreshCollection(ArrayCollection $collection)
    {
        foreach ($collection as $key => $item) {
            $collection->set($key, $this->refresh($item));
        }
        return $collection;
    }

    /**
     * Mode setter
     *
     * @param string $mode Mode
     *
     * @return CrudManager
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
     * @param CrudEntityInterface $entity Entity
     * @param bool                $flush  Flush
     *
     * @return object
     */
    public function create(CrudEntityInterface $entity, $flush = true)
    {
        $this->eventDispatcher->dispatch(
            Events::PRE_CREATE,
            new CrudEvent($entity)
        );

        $this->entityManager->persist($entity);

        if ($flush) {
            $this->flush($entity);
        }

        $this->eventDispatcher->dispatch(
            Events::POST_CREATE,
            new CrudEvent($entity)
        );
        return $entity;
    }

    /**
     * Flush
     *
     * @param CrudEntityInterface $entity Entity
     *
     * @return void
     */
    public function flush(CrudEntityInterface $entity = null)
    {
        if ($this->getMode() !== self::MODE_DRY_RUN) {
            $this->entityManager->flush($entity);
        }
    }

    /**
     * Persist
     *
     * @param CrudEntityInterface $entity Entity
     *
     * @return void
     */
    public function persist(CrudEntityInterface $entity)
    {
        $this->entityManager->persist($entity);
    }

    /**
     * Create one object
     *
     * @param CrudEntityInterface $object Object to create
     *
     * @throws ValidationFailedException
     * @return void
     */
    public function createOne(CrudEntityInterface $object)
    {
        $this->create($object, false);
        if ($object instanceof ValidatableInterface) {
            $validation = $this->validate($object);
            if ($validation instanceof ConstraintViolationListInterface) {
                $object->getViolations()->addAll($validation);
            }
            if ($object->getViolations()->count()) {
                throw new ValidationFailedException($object->getViolations());
            }
        }
        $this->flush();
    }

    /**
     * Creating collection
     *
     * @param ArrayCollection|CrudEntityInterface[] $collection        Collection
     * @param bool                                  $processUnitOfWork Process unit of work?
     *
     * @throws ValidationFailedException
     * @return void
     */
    public function createCollection(ArrayCollection $collection, $processUnitOfWork = false)
    {
        $this->eventDispatcher->dispatch(
            Events::COLLECTION_PRE_CREATE,
            new CrudCollectionEvent($collection)
        );

        if ($processUnitOfWork) {
            $unitOfWork = $this->filterCollection($collection);
            $collection = $unitOfWork->getInsertions();
        }

        $violations = $this->validateCollection($collection);

        if ($violations->count()) {
            $roots = new ArrayCollection();
            foreach ($violations as $violation) {
                $roots->add($violation->getRoot());
            }
            foreach ($collection as $collectionItem) {
                if (!$roots->contains($collectionItem)) {
                    /**
                     * This case we treat like success
                     * and save entity.
                     */
                    $this->create($collectionItem, true);
                }
            }
            throw new ValidationFailedException($violations);
        }


        foreach ($collection as $collectionItem) {
            $this->create($collectionItem, false);
        }

        $this->flush();
    }

    /**
     * Entity validation
     *
     * @param CrudEntityInterface $entity Entity
     * @return ConstraintViolationList|bool
     */
    public function validate(CrudEntityInterface $entity)
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
     * @param ArrayCollection|CrudEntityInterface[] $collection  Collection
     * @param ConstraintViolationList               &$violations Violations
     *
     * @return ConstraintViolationList
     */
    private function validateCollectionUniqueness(
        ArrayCollection $collection,
        ConstraintViolationList &$violations = null
    ) {
        if (null === $violations) {
            $violations = new ConstraintViolationList();
        }

        foreach ($collection as $collectionItem) {
            $count = 0;
            foreach ($collection as $collectionItemToCompare) {
                if ($collectionItemToCompare->getId()) {
                    if ($collectionItemToCompare->getId() == $collectionItem->getId()) {
                        $count++;
                    }
                } else {
                    if ($collectionItemToCompare->toArray() == $collectionItem->toArray()) {
                        $count++;
                    }
                }
                if ($count > 1) {
                    $collection->removeElement($collectionItem);
                    $count--;
                }
            }
        }
        return $violations;
    }

    /**
     * Collection validation
     *
     * @param ArrayCollection|CrudEntityInterface[] $collection collection
     * @throws ValidationFailedException
     * @return ConstraintViolationList
     */
    public function validateCollection(ArrayCollection $collection)
    {
        $violations = new ConstraintViolationList();
        $this->validateCollectionUniqueness($collection, $violations);
        foreach ($collection as $collectionItem) {
            $itemViolations = $this->validate($collectionItem);
            if ($itemViolations instanceof ConstraintViolationList) {
                $violations->addAll($itemViolations);
            }
        }
        return $violations;
    }

    /**
     * Update entity
     *
     * @param CrudEntityInterface $entity entity
     * @return void
     */
    public function update(CrudEntityInterface $entity)
    {
        $this->save($entity);

        $this->eventDispatcher->dispatch(
            Events::POST_UPDATE,
            new CrudEvent($entity)
        );
    }

    /**
     * Creating collection
     *
     * @param ArrayCollection|CrudEntityInterface[] $collection collection
     * @return void
     */
    public function updateCollection(ArrayCollection $collection)
    {
        foreach ($collection as $entity) {
            $this->update($entity);
        }
    }

    /**
     * Clear entity manager
     *
     * @param string|null $entityName entityName
     *
     * @return void
     */
    public function clearEntityManager($entityName = null)
    {
        $this->entityManager->clear($entityName);
    }

    /**
     * Updating one entity
     *
     * @param CrudEntityInterface $entity Entity
     * @param array               $data   Data
     *
     * @throws \Exception
     * @return void
     */
    public function setData(CrudEntityInterface $entity, array $data = array())
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
     * @param CrudEntityInterface $entity Entity
     * @throws ValidationFailedException
     * @return void
     */
    public function save(CrudEntityInterface $entity)
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
     * @param CrudEntityInterface $entity Entity
     * @throws ValidationFailedException
     *
     * @return void
     */
    public function validateExistence(CrudEntityInterface $entity)
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
     * @param ArrayCollection|CrudEntityInterface[] $collection Collection
     * @param bool                                  $replace    replace
     *
     * @return CrudUnitOfWork
     */
    public function filterCollection(ArrayCollection $collection, $replace = true)
    {
        $unitOfWork = new CrudUnitOfWork();
        foreach ($collection as $entity) {
            if ($entity->getId()) {
                $crudEntity = $this->find(get_class($entity), $entity->getId());
            } else {
                $this->crudTransformer->initializeClassMetadata(get_class($entity));
                $conditions = $this->crudTransformer->getUniqueSearchConditions($entity);
                $crudEntity = $this->entityManager->getRepository(get_class($entity))->findOneBy($conditions);
            }

            if ($crudEntity instanceof CrudEntityInterface) {
                if ($replace) {
                    $key = $collection->indexOf($entity);
                    $collection->remove($key);
                    $collection->set($key, $crudEntity);
                }
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
     * @param CrudUnitOfWork $unitOfWork UnitOfWork
     * @return void
     */
    public function processUnitOfWork(CrudUnitOfWork $unitOfWork)
    {
        if ($unitOfWork->getInsertions()->count()) {
            $this->createCollection($unitOfWork->getInsertions(), true);
        }
        if ($unitOfWork->getUpdates()->count()) {
            $this->updateCollection($unitOfWork->getUpdates(), true);
        }
    }
}
