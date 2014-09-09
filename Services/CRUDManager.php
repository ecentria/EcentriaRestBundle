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
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use Ecentria\Libraries\CoreRestBundle\Event\CRUDEvent;
use Ecentria\Libraries\CoreRestBundle\Event\Events;
use JMS\Serializer\Exception\ValidationFailedException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\RecursiveValidator;


/**
 * CRUD Manager
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CRUDManager
{
    /**
     * Entity manager
     *
     * @var EntityManagerInterface
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
     * Constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param RecursiveValidator $validator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RecursiveValidator $validator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
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
        return $entity;
    }

    /**
     * Creating collection
     *
     * @param object[]|ArrayCollection $collection
     * @return void
     */
    public function createCollection(ArrayCollection $collection)
    {
        $this->validateCollection($collection);
        foreach ($collection as $collectionItem) {
            $this->create($collectionItem, false);
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
     * @param ArrayCollection|object[] $collection
     * @return bool
     * @throws ValidationFailedException
     */
    public function validateCollection(ArrayCollection $collection)
    {
        $violations = new ConstraintViolationList();
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

    public function update($entity)
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
     * @param object $entity
     * @param array  $data
     * @return void
     */
    public function setData($entity, array $data = array())
    {
        $data = reset($data);
        foreach ($data as $name => $value) {
            if ($name === 'id') {
                continue;
            }
            if ($value !== null) {
                $classMetadata = $this->entityManager->getClassMetadata(get_class($entity));
                $property = ucfirst(Inflector::camelize($name));
                if ($classMetadata->hasAssociation($property)) {
                    $targetClass = $classMetadata->getAssociationTargetClass($property);
                    $value = $this->entityManager->getReference($targetClass, $value);
                }
            }
            $method = Inflector::camelize('set_' . $name);
            if (method_exists($entity, $method)) {
                $entity->$method($value);
            }
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
     * Persist
     *
     * @param object $entity
     */
    public function persist($entity)
    {
        $this->entityManager->persist($entity);
    }
}