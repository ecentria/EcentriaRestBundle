<?php
/**
 * Test Bootstrap
 *
 * @copyright 2015 Ecentria
 * @package   EcentriaRestBundle
 * @author    Ruslan Zavacky <ruslan.zavacky@intexsys.lv>
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
        if (0 === strpos($class, 'Ecentria\\Libraries\\CoreRestBundle\\')) {
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
