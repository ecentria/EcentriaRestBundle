Converters
==========

Converters helps to get data from request in suitable format.

ArrayCollectionConverter
------------------------

```
/**
  * @Sensio\ParamConverter(
  *      "subscriptions",
  *      class="Ecentria\Bundle\CommunicationApiBundle\Entity\Subscription",
  *      converter = "ecentria.api.converter.array_collection"
  * )
  */
```

EntityConverter
---------------

For retrieving doctrine entities based upon simple id parameters contained in the request.

```
/**
  * @Sensio\ParamConverter(
  *      "subscription",
  *      class="Ecentria\Bundle\CommunicationApiBundle\Entity\Subscription",
  *      converter = "ecentria.api.converter.entity"
  * )
  *
  * @Route("/subscription/{id}", ...
  */
```

Also includes a mode for creation of entities with potential one to one and one to many relationships.

```
/**
 * @Sensio\ParamConverter(
 *      "credit",
 *      class="Ecentria\CustomerApiBundle\Entity\Credit",
 *      converter = "ecentria.api.converter.entity",
 *      options = {
 *          "mode" = "create",
 *          "references" = {
 *              "id" = "accountId",
 *              "class" = "Ecentria\CustomerApiBundle\Entity\Account",
 *              "name" = "account"
 *          }
 *      }
 * )
 *
 * @Route("/account/{accountId}/credit", ...
 *
 */
```

JsonConverter
-------------

```
/**
  * @Sensio\ParamConverter(
  *      "data",
  *      converter = "ecentria.api.converter.json"
  * )
  */
```

ParameterConverter
------------------

```
/**
  * @Sensio\ParamConverter(
  *      converter = “ecentria.api.converter.parameter”,
  *      options = {
  *           "parameters" = {"hash"}
  *      }
  * )
  */
```