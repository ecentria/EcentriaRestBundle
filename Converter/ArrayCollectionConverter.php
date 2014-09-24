<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Ecentria\Libraries\CoreRestBundle\EventListener\ExceptionListener;
use Ecentria\Libraries\CoreRestBundle\Services\CRUDTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * CRUD Transformer
     *
     * @var CRUDTransformer
     */
    private $crudTransformer;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param CRUDTransformer $crudTransformer
     */
    function __construct(EntityManager $entityManager, CRUDTransformer $crudTransformer)
    {
        $this->entityManager = $entityManager;
        $this->crudTransformer = $crudTransformer;
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
        $this->crudTransformer->initializeClassMetadata($class);
        foreach ($items as $item) {
            $object = new $class();
            foreach ($item as $property => $value) {
                $this->crudTransformer->processPropertyValue($object, $property, $value, 'create', $collection);
            }
            $collection->add($object);
        }
        $request->attributes->set($name, $collection);

        /** This attribute added to support exception listener */
        $request->attributes->set(ExceptionListener::DATA_ALIAS, $name);

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