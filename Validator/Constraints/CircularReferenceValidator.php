<?php
/*
 * This file is part of the OpCart software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\CoreRestBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Circular reference validator
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 *
 * TODO: make validator to work with different identifiers. getId() - must be reworked.
 */
class CircularReferenceValidator extends ConstraintValidator
{
    /**
     * Current parent
     *
     * @var object
     */
    protected $parent;

    /**
     * Current entity
     *
     * @var object
     */
    protected $entity;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Entity manager setter
     *
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($entity, Constraint $constraint)
    {
        $parent = $entity->getParent();
        $this->entity = $entity;
        $this->parent = $parent;
        if ($parent) {
            $criticalError = false;
            try {
                $this->entityManager->initializeObject($parent);
            } catch (EntityNotFoundException $exception) {
                $this->addViolationAt($parent->getId(), $this->entity->getId(), null, 'Parent #%s of object #%s does not exist');
                $criticalError = true;
            }
            if ($criticalError === false) {
                $this->validateParent($entity, $parent);
            }
        }
    }

    /**
     * Parent validation
     *
     * @param object $entity
     * @param object $parent
     */
    private function validateParent($entity, $parent)
    {
        if ($entity === $parent) {
            $this->addViolationAt($parent->getId(), $entity->getId());
        }
        foreach ($this->getParents($parent) as $channel) {
            if ($entity === $channel) {
                $this->addViolationAt($parent->getId(), $entity->getId());
            }
        }
    }

    /**
     * Adding violation at element
     *
     * @param mixed $parentId
     * @param mixed $childId
     * @param string|null $name
     * @param string|null $message
     */
    private function addViolationAt($parentId, $childId = null, $name = null, $message = null)
    {
        $this->context->addViolationAt(
            $name ? $name : 'Parent',
            sprintf(
                $message ? $message : 'You cannot set object #%s as parent for object #%s because of circular reference',
                $parentId,
                $childId ? $childId : 'undefined'
            )
        );
    }

    /**
     * Returns all parent channels for given channel
     *
     * @param object $channel
     * @param array   $parents
     * @return object[]|array
     */
    private function getParents($channel, array $parents = array())
    {
        try {
            $parent = $channel->getParent();
        } catch (EntityNotFoundException $exception) {
            $this->addViolationAt($this->parent->getId(), $this->entity->getId(), null, 'Parent #%s of object #%s does not exist');
            return $parents;
        }

        if (!is_null($parent)) {
            $parents[] = $parent;
            if ($parent !== $this->parent) {
                return $this->getParents($parent, $parents);
            }
        }
        return $parents;
    }
}
