<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Ecentria\Libraries\EcentriaRestBundle\Converter;

use Ecentria\Libraries\EcentriaRestBundle\Model\Alias;
use Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudEntityInterface;
use Ecentria\Libraries\EcentriaRestBundle\Model\Validatable\ValidatableInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter as BaseDoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Ecentria\Libraries\EcentriaRestBundle\Services\CRUD\CrudTransformer;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Modified DoctrineParamConverter.
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class EntityConverter extends BaseDoctrineParamConverter
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
    public function __construct(CrudTransformer $crudTransformer, ManagerRegistry $registry = null)
    {
        $this->crudTransformer = $crudTransformer;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name    = $configuration->getName();
        $class   = $configuration->getClass();
        $options = $this->getOptions($configuration);
        $create  = !empty($options['mode']) && $options['mode'] == 'create';

        if (null === $request->attributes->get($name, false)) {
            $configuration->setIsOptional(true);
        }

        $object = $create ? null : $this->findObject($class, $request, $options, $name);
        if (is_null($object)) {
            $object = $this->createNewObject($class, $request, $create, $options);
        }

        $request->attributes->set($name, $object);

        /**
         * Alias to access current collection
         * Used by exception listener
         */
        $request->attributes->set(Alias::DATA, $name);

        return true;
    }

    /**
     * @param $class
     * @param $request
     * @param $options
     * @param $name
     * @return bool|mixed
     */
    private function findObject($class, $request, $options, $name)
    {
        $object = null;
        // find by identifier?
        if (null === $object = $this->find($class, $request, $options, $name)) {
            // find by criteria
            $object = $this->findOneBy($class, $request, $options);
        }
        return $object;
    }

    /**
     * Create New Object
     *
     * @param string  $class
     * @param Request $request
     * @param bool    $create
     * @return CrudEntityInterface|mixed
     */
    private function createNewObject($class, Request $request, $create, $options)
    {
        $data = $create ? json_decode($request->getContent(), true) : array();
        $object = $this->crudTransformer->arrayToObject($data, $class);
        if ($object instanceof ValidatableInterface && $create) {
            $violations = $this->crudTransformer->arrayToObjectPropertyValidation($data, $class);
            $valid = !((bool) $violations->count());
            $object->setViolations($violations);
            $object->setValid($valid);
        }
        if (!$create && $object instanceof CrudEntityInterface) {
            $object->setId($request->attributes->get('id'));
        }
        if (isset($options['references'])) {
            $references = !is_array(current($options['references'])) ? array($options['references']) : $options['references'];
            foreach ($references as $reference) {
                $entity = $this->findObject(
                    $reference['class'], $request, array_merge($reference, $options), $reference['name']
                );
                $setter = $this->crudTransformer->getPropertySetter($reference['name']);
                if (method_exists($object, $setter) && is_object($entity)) {
                    $object->$setter($entity);
                }
            }
        }
        return $object;
    }
}
