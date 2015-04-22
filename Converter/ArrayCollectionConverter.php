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
use Ecentria\Libraries\CoreRestBundle\EventListener\ExceptionListener;
use Ecentria\Libraries\CoreRestBundle\Services\CRUD\CrudTransformer;
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
     * CRUD Transformer
     *
     * @var CrudTransformer
     */
    private $crudTransformer;

    /**
     * Constructor
     *
     * @param CrudTransformer $crudTransformer crudTransformer
     */
    public function __construct(CrudTransformer $crudTransformer)
    {
        $this->crudTransformer = $crudTransformer;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool    True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();
        $items = json_decode($request->getContent(), true);
        $collection = new ArrayCollection();

        if (is_array($items)) {
            $this->crudTransformer->initializeClassMetadata($class);
            foreach ($items as $item) {
                $object = new $class();
                foreach ($item as $property => $value) {
                    $this->crudTransformer->processPropertyValue($object, $property, $value, 'create', $collection);
                }
                $collection->add($object);
            }
        }

        $request->attributes->set($name, $collection);

        /** This attribute added to support exception listener */
        $request->attributes->set(ExceptionListener::DATA_ALIAS, $name);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool    True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return true;
    }
}
