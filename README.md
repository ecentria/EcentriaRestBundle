Ecentria Core REST Bundle
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

## D) Update app/config/routing.yml

Modify app/config/routing.yml and add the following:

``` yaml
_ecentria_libnraries_core_rest_bundle:
    resource: "@EcentriaLibrariesCoreRestBundle/Resources/config/routing.yml"

NelmioApiDocBundle:
    resource: "@NelmioApiDocBundle/Resources/config/routing.yml"
    prefix:   /api/doc
```


## E) Using CRUDEntity with Transactions

First of all you need to extend you entity from CRUDEntity.

Controller for entity must be annotated as:

``` php
<?php

/**
 * @EcentriaAnnotation\Transactional(
 *      model="Your\Entity\Path",
 *      relatedRoute="your_get_entity_route"
 * )
 */
 ```

Every action that needs to work with transaction must end with:

``` php
<?php

return $this->viewTransaction(...);
```

To avoid action working with transaction use annotation:

``` php
<?php

@EcentriaAnnotation\AvoidTransaction()
```

## That's it!
Everything is in place to start building out REST services.