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
     * @param CrudTransformer      $crudTransformer crudTransformer
     * @param ManagerRegistry|null $registry        Registry
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
            $this->validateNewObject($object, $class, $data, $options);
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
                    array_merge($reference, $options, ['data' => $data]),
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
     * Validate new object
     *
     * @param ValidatableInterface $object  Object
     * @param string               $class   Object class name
     * @param array                $data    Object generation data
     * @param array                $options Object generation options
     * @return void
     */
    private function validateNewObject($object, $class, $data, $options)
    {
        $referenceProperties = [];
        // Remove any properties are are used for setting reference objects
        if (isset($options['references'])) {
            foreach ($options['references'] as $reference) {
                if (isset($reference['property'])) {
                    $referenceProperties[] = $reference['property'];
                }
            }
        }
        $violations = $this->crudTransformer->arrayToObjectPropertyValidation(
            array_diff_key($data, array_flip($referenceProperties)),
            $class
        );
        $valid = !((bool) $violations->count());
        $object->setViolations($violations);
        $object->setValid($valid);
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

    /**
     * Get ID for new object
     *
     * @param Request $request
     * @param array   $options
     * @param string  $name
     * @return array|bool|mixed
     */
    protected function getIdentifier(Request $request, $options, $name)
    {
        if (isset($options['id'])) {
            if (!is_array($options['id'])) {
                $name = $options['id'];
            } elseif (is_array($options['id'])) {
                $id = array();
                foreach ($options['id'] as $field) {
                    $id[$field] = $request->attributes->get($field);
                }

                return $id;
            }
        }

        if (isset($options['property']) && isset($options['data']) && isset($options['data'][$options['property']])) {
            return $options['data'][$options['property']];
        }

        if ($request->attributes->has($name)) {
            return $request->attributes->get($name);
        }

        if ($request->attributes->has('id') && !isset($options['id'])) {
            return $request->attributes->get('id');
        }

        return false;
    }
}
