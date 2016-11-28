<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2016, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * RelatedRouteForAction
 *
 * @Annotation
 * @Target("METHOD")
 *
 * Used to specify a related route for the controller action
 *
 * @author Son Dang <son.dang@opticsplanet.com>
 */
class RelatedRouteForAction extends Annotation
{
    const NAME = 'Ecentria\\Libraries\\EcentriaRestBundle\\Annotation\\RelatedRouteForAction';

    /**
     * Name of the related route that will override the one set at the class level
     *
     * @var string
     */
    public $routeName;
}
