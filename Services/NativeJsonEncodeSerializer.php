<?php
namespace Ecentria\Libraries\CoreRestBundle\Services;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class NativeJsonEncodeSerializer implements SerializerInterface {

    /**
     * Serializes the given data to the specified output format.
     *
     * @param object|array|scalar $data
     * @param string $format
     * @param Context $context
     *
     * @return string
     */
    public function serialize($data, $format, SerializationContext $context = null) {
        $this->checkMediaType($format);
        return json_encode($data);
    }

    /**
     * Deserializes the given data to the specified type.
     *
     * @param string $data
     * @param string $type
     * @param string $format
     * @param Context $context
     *
     * @return object|array|scalar
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
    private function checkMediaType($type) {
        if ($type !== 'json') {
            throw new UnsupportedMediaTypeHttpException("'$type' is not supported.");
        }
    }
}

