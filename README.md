Ecentria Core REST Bundle v0.1.4
=========================

This bundle is the core REST bundle that all REST services built within Ecentria will extend.
Initially, this bundle contains the following:
    1) FOSRestBundle & JMSSerializerBundle setup
    2) Pre-set routes for /ping and /status for monitoring

As more services are created, this bundle will grow to include commonly-needed functionality.

Installation
------------

## A) Install Ecentria Generic Service Bundle

Add the repository to your composer.json file 
```
    "repositories": [
        ...
        { "type": "vcs", "url": "ssh://git@stash.dev.opticplanet.net/lib/corerestbundle.git" }
    ]
```
Add via composer command

``` bash
$ php composer.phar require ecentria/core-rest-bundle dev-master
```

Or Add via composer.json directly
```
"ecentria/core-rest-bundle": "dev-master"
```

## B) Enable the bundle

Enable the bundle, as well as the FOSRestBundle and JMSSerializerBundle in the kernal:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
         new Ecentria\Libraries\CoreRestBundle\EcentriaLibrariesCoreRestBundle(),
         new FOS\RestBundle\FOSRestBundle(),
         new JMS\SerializerBundle\JMSSerializerBundle(),
         new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
    );
}
```

## C) Update app/config/config.yml

Modify app/config/config.yml and add the following:

``` yaml
sensio_framework_extra:
    view:
        annotations: false

fos_rest:
    param_fetcher_listener: true
    body_listener: true
    format_listener: true
    routing_loader:
            default_format: json
    view:
        view_response_listener: 'force'

nelmio_api_doc: ~
```

If you want use native json_encode/decode in your project instead of JMS\Serializer, add one more option
to the end of fos_rest section:

``` yaml

fos_rest:
    ...
    service:
        serializer: ecentria.fos_rest.native_json_encode_serializer

```

## D) Update app/config/routing.yml

Modify app/config/routing.yml and add the following:

``` yaml
_ecentria_libnraries_core_rest_bundle:
    resource: "@EcentriaLibrariesCoreRestBundle/Resources/config/routing.yml"

NelmioApiDocBundle:
    resource: "@NelmioApiDocBundle/Resources/config/routing.yml"
    prefix:   /api/doc
```


Annotations
-----
-----

There are several useful annotations.

 * ### Transactional
Used for controller to enable transaction system.
    
    **model**

        Every controller must work with defined resource.
        Model parameter should be equal to full path to your
        entity that current controller works with.

    **relatedRoute**

        Every model should have route leading to get action.
        Related route parameter must be equal to current route name.

    Example:

        use Ecentria\Libraries\CoreRestBundle\Annotation as EcentriaAnnotation;
        
        /**
         * @EcentriaAnnotation\Transactional(
         *   model="Path to you entity",
         *   relatedRoute="your_get_entity_route"
         * )
         */
        
* ### AvoidTransaction

    Used for controller action to avoid creating transaction.

        use Ecentria\Libraries\CoreRestBundle\Annotation as EcentriaAnnotation;
        
        /**
         * @EcentriaAnnotation\AvoidTransaction()
         */
        
* ### PropertyRestriction
Used for model (entity) property to avoid update or create.
As parameter it gets array of actions: {“update”, “create"}

        use Ecentria\Libraries\CoreRestBundle\Annotation as EcentriaAnnotation;
        
        /**
         * @EcentriaAnnotation\PropertyRestriction({"update", "create"})
         */
         

## That's it!
Everything is in place to start building out REST services.