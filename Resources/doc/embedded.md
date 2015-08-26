Embedded data
==========

Embedded data allows you to get additional information about associated entities, objects, fields, links or any data that you want to embed.

How to describe serialization for entity
------------------------

```
YourBundle\Entity\User:

    properties:
        address:
            exclude: true

    relations:
        -
            rel: address
            embedded:
                content: expr(object.getAddress())
                exclusion:
                    exclude_if: expr(object.getAddress() === null)
                    groups:
                        - embedded.address
```

How to get embedded data 
------------------------
For example you have simple GET resource action:

**/api/user/1**

With response:

```
{
    "id": 1,
    "name": "John",
    "status": "active"
}
```

To get embedded information you should add ```?_embed=address```

**/api/user/1?_embed=address**

And response will turn into:

```
{
    "id": 1,
    "name": "John",
    "status": "active",
    "_embedded": {
        "address": {
            "id": 1
        }
    }
}
```

Usage variants:
--------------

```?_embed=address``` first level
```?_embed=address.country``` second level
```?_embed=address,orders``` two or more object can be embedded
```?_embed=address.country,orders,credits```


Predefined values:
------------------

```Default``` - default serialization group.
```all``` - embed all entity information
```violation.entity``` - allows to show all embedded data for example on incorrect PATCH
```violation.collection``` - allows to show all embedded data for example on incorrect POST

Backward compatibility:
-----------------------

```?_embedded=true``` is the same as ```?_embed=all```