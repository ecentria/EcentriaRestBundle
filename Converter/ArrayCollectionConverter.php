<?php
/*
 * This file is part of the OpCart software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Array collection converter
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class ArrayCollectionConverter implements ParamConverterInterface
{
    /**
     * Entity manager
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     */
    function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();
        $items = json_decode($request->getContent(), true);
        $collection = new ArrayCollection();
        if (!is_array($items)) {
            return false;
        }
        foreach ($items as $item) {
            $object = new $class();
            foreach ($item as $key => $value) {
                $method = Inflector::camelize('set_' . $key);
                if ($value !== null) {
                    $classMetadata = $this->entityManager->getClassMetadata($class);
                    $property = ucfirst(Inflector::camelize($key));
                    if ($classMetadata->hasAssociation($property)) {
                        $targetClass = $classMetadata->getAssociationTargetClass($property);
                        $associatedObject = null;
                        foreach ($collection as $collectionItem) {
                            $identifierFieldName = $classMetadata->getSingleIdentifierFieldName();
                            $identifierGetter = Inflector::camelize('get_' . $identifierFieldName);
                            if ($collectionItem->$identifierGetter() === $value) {
                                $associatedObject = $collectionItem;
                            }
                        }
                        if (is_null($associatedObject)) {
                            $associatedObject = $this->entityManager->getReference($targetClass, $value);
                        }
                        $value = $associatedObject;

                    }
                }
                if (method_exists($object, $method)) {
                    $object->$method($value);
                }
            }
            $collection->add($object);
        }
        $request->attributes->set($name, $collection);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return true;
    }
}