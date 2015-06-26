Setting up the bundle
=====================

A: Install Ecentria Generic Service Bundle
------------------------------------------

Add the repository to your composer.json file 

    "repositories": [
        ...
        { "type": "vcs", "url": "https://github.com/ecentria/EcentriaRestBundle" }
    ]

Add via composer command

    $ php composer.phar require ecentria/ecentria-rest-bundle dev-master

Or Add via composer.json directly

    "ecentria/ecentria-rest-bundle": "dev-master"

B: Enable the bundle
--------------------

Enable the bundle, as well as the FOSRestBundle and JMSSerializerBundle in the kernal:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
         new Ecentria\Libraries\EcentriaRestBundle\EcentriaRestBundle(),
         new FOS\RestBundle\FOSRestBundle(),
         new JMS\SerializerBundle\JMSSerializerBundle(),
         new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
    );
}
```

C: Update app/config/config.yml
-------------------------------

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

D: Update app/config/routing.yml
--------------------------------

Modify app/config/routing.yml and add the following:

``` yaml
_ecentria_rest_bundle:
    resource: "@EcentriaRestBundle/Resources/config/routing.yml"

NelmioApiDocBundle:
    resource: "@NelmioApiDocBundle/Resources/config/routing.yml"
    prefix:   /api/doc
```