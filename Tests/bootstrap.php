<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__ . '/../vendor/autoload.php';

if (!$loader) {
    die(<<<'EOT'
You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
EOT
    );
}

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

spl_autoload_register(
    function ($class) {
        if (0 === strpos($class, 'Ecentria\\Libraries\\EcentriaRestBundle\\')) {
            $path = __DIR__ . '/../' . implode('/', array_slice(explode('\\', $class), 2)) . '.php';
            if (!stream_resolve_include_path($path)) {
                return false;
            }
            require_once $path;
            return true;
        }
        return false;
    }
);

return $loader;
