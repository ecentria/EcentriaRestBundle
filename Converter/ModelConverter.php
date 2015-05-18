<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2015, Ecentria, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Converter;

use Ecentria\Libraries\CoreRestBundle\Model\Alias;
use Ecentria\Libraries\CoreRestBundle\Model\Validatable\ValidatableInterface;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * Array collection converter
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class ModelConverter implements ParamConverterInterface
{
    /**
     * Serializer
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * Validator
     *
     * @var RecursiveValidator
     */
    private $validator;

    /**
     * Constructor
     *
     * @param Serializer         $serializer Serializer
     * @param RecursiveValidator $validator  Validator
     */
    public function __construct(Serializer $serializer, RecursiveValidator $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
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
        $options = $configuration->getOptions();

        if (isset($options['query'])) {
            $content = new \stdClass();
            $metadata = $this->serializer->getMetadataFactory()->getMetadataForClass($class);
            foreach ($metadata->propertyMetadata as $propertyMetadata) {
                if (!$propertyMetadata->readOnly) {
                    $property = $propertyMetadata->name;
                    $value = $request->query->get($propertyMetadata->name);
                    if (!is_null($value)) {
                        $content->$property = $request->query->get($propertyMetadata->name);
                    }
                }
            }
            $content = json_encode($content);
        } else {
            $content = $request->getContent();
        }


        $success = false;
        try {
            $model = $this->serializer->deserialize($content, $class, 'json');
            $success = true;
        } catch (\Exception $e) {
            $model = new $class();
        }

        /**
         * Validate if possible
         */
        if ($model instanceof ValidatableInterface) {
            $violations = $this->validator->validate($model);
            $valid = $success && !((bool) $violations->count());
            $model->setViolations($violations);
            $model->setValid($valid);
        }

        /**
         * Adding transformed collection
         * to request attribute.
         */
        $request->attributes->set($name, $model);

        /**
         * Alias to access current collection
         * Used by exception listener
         */
        $request->attributes->set(Alias::DATA, $name);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return true;
    }
}
