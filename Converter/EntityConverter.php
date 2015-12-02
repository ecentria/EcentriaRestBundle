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
    const MODE_CREATE = 'create';
    const MODE_RETRIEVE = 'retrieve';
    const MODE_UPDATE = 'update';

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
        $mode  = empty($options['mode']) ? self::MODE_RETRIEVE : $options['mode'];

        if (null === $request->attributes->get($name, false)) {
            $configuration->setIsOptional(true);
        }

        $object = $mode == self::MODE_CREATE ? null : $this->findObject($class, $request, $options, $name);
        if (empty($object) || $mode == self::MODE_UPDATE) {
            $object = $this->createOrUpdateNewObject($class, $request, $mode, $options, $object);
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
     * @param string         $class   Class name
     * @param Request        $request HTTP request
     * @param string         $mode    Create, Retrieve, Update
     * @param array          $options Param converter options
     * @param object|boolean $object  Object
     * @throws \RuntimeException
     * @return CrudEntityInterface|mixed
     */
    public function createOrUpdateNewObject($class, Request $request, $mode, $options, $object)
    {
        $ids = [];
        $data = $mode == self::MODE_RETRIEVE ? [] : json_decode($request->getContent(), true);
        if (!is_array($data)) {
            throw new RuntimeException('Invalid JSON request content');
        }
        // Convert array into object and test validity
        if ($mode != self::MODE_UPDATE || $object == false) {
            $object = new $class();
        }
        $object = $this->crudTransformer->arrayToObject($data, $class, $object);
        if ($object instanceof ValidatableInterface && $mode != self::MODE_RETRIEVE) {
            $violations = $this->crudTransformer->arrayToObjectPropertyValidation($data, $class);
            $valid = !((bool) $violations->count());
            $object->setViolations($violations);
            $object->setValid($valid);
        }
        // Get list of ids from request attributes
        if (($mode == self::MODE_RETRIEVE || (isset($options['generated_id']) && !$options['generated_id'])) &&
            $object instanceof CrudEntityInterface
        ) {
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
