<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * NativeJsonEncodeSerializer
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class NativeJsonEncodeSerializer implements SerializerInterface
{

    /**
     * Serializes the given data to the specified output format.
     *
     * @param object|array         $data
     * @param string               $format
     * @param SerializationContext $context
     *
     * @return string
     */
    public function serialize($data, $format, SerializationContext $context = null)
    {
        $this->checkMediaType($format);
        return json_encode($data);
    }

    /**
     * Deserializes the given data to the specified type.
     *
     * @param string                 $data
     * @param string                 $type
     * @param string                 $format
     * @param DeserializationContext $context
     *
     * @return object|array
     */
    public function deserialize($data, $type, $format, DeserializationContext $context = null)
    {
        $this->checkMediaType($format);
        return json_decode($data);
    }

    /**
     * Throws UnsupportedMediaTypeHttpException
     *
     * @param string $type The type
     * @return void
     * @throws UnsupportedMediaTypeHttpException
     */
    private function checkMediaType($type)
    {
        if ($type !== 'json') {
            throw new UnsupportedMediaTypeHttpException("'$type' is not supported.");
        }
    }
}
