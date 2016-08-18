<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\EcentriaRestBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Circular reference validator
 * TODO: make validator to work with different identifiers. getId() - must be reworked.
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
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
     * Manager Registry
     *
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * Entity manager setter
     *
     * @param ManagerRegistry $registry
     *
     * @return void
     */
    public function setRegistry(ManagerRegistry $registry)
    {
        $this->registry = $registry;
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
            $objectManager = $this->registry->getManagerForClass(get_class($entity));
            try {
                $objectManager->initializeObject($parent);
            } catch (EntityNotFoundException $exception) {
                $parentMetadata = $objectManager->getClassMetadata(get_class($parent));
                $parentIdValues = $parentMetadata->getIdentifierValues($parent);
                $this->addViolationAt(current($parentIdValues), $this->entity->getPrimaryKey(), null, 'Parent #%s of object #%s does not exist');
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
     *
     * @return void
     */
    private function validateParent($entity, $parent)
    {
        if ($entity === $parent) {
            $this->addViolationAt($parent->getPrimaryKey(), $entity->getPrimaryKey());
        }
        foreach ($this->getParents($parent) as $channel) {
            if ($entity === $channel) {
                $this->addViolationAt($parent->getPrimaryKey(), $entity->getPrimaryKey());
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
     *
     * @return void
     */
    private function addViolationAt($parentId, $childId = null, $name = null, $message = null)
    {
        $this
            ->context
            ->buildViolation(
                sprintf(
                    $message ? $message : 'You cannot set object #%s as parent for object #%s because of circular reference',
                    $parentId,
                    $childId ? $childId : 'undefined'
                )
            )
            ->atPath(
                $name ? $name : 'Parent'
            )
            ->addViolation();
    }

    /**
     * Returns all parent channels for given channel
     *
     * @param mixed   $channel
     * @param array   $parents
     *
     * @return mixed|array
     */
    private function getParents($channel, array $parents = array())
    {
        try {
            $parent = $channel->getParent();
        } catch (EntityNotFoundException $exception) {
            $this->addViolationAt($this->parent->getPrimaryKey(), $this->entity->getPrimaryKey(), null, 'Parent #%s of object #%s does not exist');
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
