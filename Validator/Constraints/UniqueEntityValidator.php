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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator as BaseUniqueEntityValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Unique Entity Validator extension for referenced classes
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class UniqueEntityValidator extends BaseUniqueEntityValidator
{
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
        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\UniqueEntity');
        }

        $criticalError = false;
        $entityClass = get_class($entity);
        $class = $this->registry->getManagerForClass($entityClass)->getClassMetadata($entityClass);
        foreach ($constraint->fields as $fieldName) {
            $object = $class->reflFields[$fieldName]->getValue($entity);
            try {
                $this->registry->getManagerForClass($entityClass)->initializeObject($object);
            } catch (EntityNotFoundException $exception) {
                $this
                    ->context
                    ->buildViolation($fieldName . ' does not exist')
                    ->atPath($fieldName)
                    ->addViolation();
                $criticalError = true;
            }
        }

        if ($criticalError === false) {
            parent::validate($entity, $constraint);
        }

    }
}
