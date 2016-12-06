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
use Symfony\Component\Stopwatch\Stopwatch;
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
        $stopWatch = new Stopwatch();
        $stopWatch->start('EntityConverter');
        $name    = $configuration->getName();
        $class   = $configuration->getClass();
        $options = $this->getOptions($configuration);
        $mode  = empty($options['mode']) ? CrudTransformer::MODE_RETRIEVE : $options['mode'];
        $times = array();

        if (null === $request->attributes->get($name, false)) {
            $configuration->setIsOptional(true);
        }

        $stopWatch->start('findObject');
        $object = $mode == CrudTransformer::MODE_CREATE ? null : $this->findObject($class, $request, $options, $name);
        $stopWatch->stop('findObject');
        $times['findObject'] = $stopWatch->getEvent('findObject')->getDuration();
        if (empty($object) || $mode == CrudTransformer::MODE_UPDATE) {
            $stopWatch->start('crudTransformer');
            $data = $this->crudTransformer->getRequestData($request, $mode);
            $this->crudTransformer->convertArrayToEntityAndValidate($data, $class, $mode, $object);
            $this->crudTransformer->setIdsFromRequest($object, $request, $mode, !empty($options['generated_id']));
            $stopWatch->stop('crudTransformer');
            $times['crudTransformer'] = $stopWatch->getEvent('crudTransformer')->getDuration();
            if (isset($options['references'])) {
                $stopWatch->start('convertExternalReferences');
                $this->convertExternalReferences($request, $object, $options);
                $stopWatch->stop('convertExternalReferences');
                $times['convertExternalReferences'] = $stopWatch->getEvent('convertExternalReferences')->getDuration();
            }
        }

        $request->attributes->set($name, $object);

        /**
         * Alias to access current collection
         * Used by exception listener
         */
        $request->attributes->set(Alias::DATA, $name);

        $stopWatch->stop('EntityConverter');
        $times['EntityConverter'] = $stopWatch->getEvent('EntityConverter')->getDuration();
        $request->attributes->set('methodTimes', $times);
        return true;
    }

    /**
     * Convert external relationships from the request to associations on the object
     *
     * @param Request             $request Request
     * @param CrudEntityInterface $object  Object
     * @param array               $options Options
     * @return void
     */
    public function convertExternalReferences(Request $request, $object, $options)
    {
        // Convert external entity references into associated objects
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
