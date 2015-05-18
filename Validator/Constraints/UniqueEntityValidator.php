<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\CoreRestBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
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
     * Entity Manager
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Entity manager setter
     *
     * @param EntityManager $entityManager
     *
     * @return void
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
        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\UniqueEntity');
        }

        $criticalError = false;
        $class = $this->entityManager->getClassMetadata(get_class($entity));
        foreach ($constraint->fields as $fieldName) {
            $object = $class->reflFields[$fieldName]->getValue($entity);
            try {
                $this->entityManager->initializeObject($object);
            } catch (EntityNotFoundException $exception) {
                $this->context->addViolationAt($fieldName, $fieldName . ' does not exist');
                $criticalError = true;
            }
        }

        if ($criticalError === false) {
            parent::validate($entity, $constraint);
        }

    }
}
