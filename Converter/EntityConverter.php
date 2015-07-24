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
use Symfony\Component\Validator\Exception\RuntimeException;

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
     * @param ManagerRegistry|null $registry        Registry
     * @param CrudTransformer      $crudTransformer crudTransformer
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
     * Create New Object
     *
     * @param string  $class   Class name
     * @param Request $request HTTP request
     * @param bool    $create  Should a missing object be created?
     * @param array   $options Param converter options
     * @throws \RuntimeException
     * @return CrudEntityInterface|mixed
     */
    public function createNewObject($class, Request $request, $create, $options)
    {
        $ids = [];
        $data = $create ? json_decode($request->getContent(), true) : [];
        if (!is_array($data)) {
            throw new RuntimeException('Invalid JSON request content');
        }
        // Convert array into object and test validity
        $object = $this->crudTransformer->arrayToObject($data, $class);
        if ($object instanceof ValidatableInterface && $create) {
            $violations = $this->crudTransformer->arrayToObjectPropertyValidation($data, $class);
            $valid = !((bool) $violations->count());
            $object->setViolations($violations);
            $object->setValid($valid);
        }
        // Get list of ids from request attributes
        if (!$create && $object instanceof CrudEntityInterface) {
            foreach ($object->getIds() as $field => $value) {
                $ids[$field] = $request->attributes->get($field);
            }
            $object->setIds($ids);
        }
        // Convert external entity references into associated objects
        if (isset($options['references'])) {
            $references = !is_array(current($options['references'])) ? array($options['references']) : $options['references'];
            foreach ($references as $reference) {
                $entity = $this->findObject(
                    $reference['class'],
                    $request,
                    array_merge($reference, $options),
                    $reference['name']
                );
                $setter = $this->crudTransformer->getPropertySetter($reference['name']);
                if (method_exists($object, $setter) && is_object($entity)) {
                    $object->$setter($entity);
                }
            }
        }
        return $object;
    }

    /**
     * Find object
     *
     * @param string  $class   Class name
     * @param Request $request HTTP request
     * @param array   $options Param converter options
     * @param string  $name    Name of object
     * @return bool|mixed
     */
    private function findObject($class, Request $request, array $options, $name)
    {
        $object = null;
        // find by identifier?
        if (null === $object = $this->find($class, $request, $options, $name)) {
            // find by criteria
            $object = $this->findOneBy($class, $request, $options);
        }
        return $object;
    }
}
