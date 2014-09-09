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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Simple json param converter
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class JsonConverter implements ParamConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $options = $configuration->getOptions();
        $assoc = true;
        if (isset($options['assoc']) && is_bool($options['assoc'])) {
            $assoc = $options['assoc'];
        }
        $value = json_decode($request->getContent(), $assoc);
        $request->attributes->set($name, $value);
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