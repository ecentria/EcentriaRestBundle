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

use Ecentria\Libraries\CoreRestBundle\EventListener\ExceptionListener;
use Ecentria\Libraries\CoreRestBundle\Interfaces\CRUDEntityInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter as BaseDoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Modified DoctrineParamConverter.
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class DoctrineParamConverter extends BaseDoctrineParamConverter
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name    = $configuration->getName();
        $class   = $configuration->getClass();

        $options = $this->getOptions($configuration);
        if (null === $request->attributes->get($name, false)) {
            $configuration->setIsOptional(true);
        }

        // find by identifier?
        if (null === $object = $this->find($class, $request, $options, $name)) {
            // find by criteria
            if (null === $object = $this->findOneBy($class, $request, $options)) {
                $object = new $class;
                if ($object instanceof CRUDEntityInterface) {
                    $object->setId($request->attributes->get('id'));
                }
            }
        }

        $request->attributes->set($name, $object);

        /** This attribute added to support exception listener */
        $request->attributes->set(ExceptionListener::DATA_ALIAS, $name);

        return true;
    }
}
