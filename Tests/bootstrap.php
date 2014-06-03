<?php
use Doctrine\Common\Annotations\AnnotationRegistry;

call_user_func(function() {
    if (! is_file($autoloadFile = __DIR__.'/../../../../../autoload.php')) {
        throw new \RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
    }

    $loader = require $autoloadFile;
    $loader->add('Ecentria\Libraries\CoreRestBundle\Tests', __DIR__);

    AnnotationRegistry::registerLoader('class_exists');
    //AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/phpcr-odm/lib/Doctrine/ODM/PHPCR/Mapping/Annotations/DoctrineAnnotations.php');
});