Converters
==========

Converters helps to get data from request in suitable format.

ArrayCollectionConverter
------------------------

/**
  * @Sensio\ParamConverter(
  *      "subscriptions",
  *      class="Ecentria\Bundle\CommunicationApiBundle\Entity\Subscription",
  *      converter = "ecentria.api.converter.array_collection"
  * )
  */ 

EntityConverter
---------------

/**
  * @Sensio\ParamConverter(
  *      "subscription",
  *      class="Ecentria\Bundle\CommunicationApiBundle\Entity\Subscription",
  *      converter = "ecentria.api.converter.entity"
  * )
  */

JsonConverter
-------------

/**
  * @Sensio\ParamConverter(
  *      "data",
  *      converter = "ecentria.api.converter.json"
  * )
  */ 

ParameterConverter
------------------

/**
  * @Sensio\ParamConverter(
  *      converter = “ecentria.api.converter.parameter”,
  *      options = {
  *           "parameters" = {"hash"}
  *      }
  * )
  */