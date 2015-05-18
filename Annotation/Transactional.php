<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * Transactional
 *
 * @Annotation
 *
 * Used for controller to enable transaction system.
 *
 * - model
 *
 * Every controller must work with defined resource.
 * Model parameter should be equal to full path to your entity
 * that current controller works with.
 *
 * - relatedRoute
 *
 * Every model should have route leading to get action.
 * Related route parameter must be equal to current route name.
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class Transactional extends ConfigurationAnnotation
{
    const NAME = 'Ecentria\\Libraries\\CoreRestBundle\\Annotation\\Transactional';

    /**
     * Model (entity namespace)
     *
     * @var string
     */
    public $model;

    /**
     * Related entity get route
     *
     * @var string
     */
    public $relatedRoute;

    /**
     * {@inheritDoc}
     */
    final public function __construct (array $data)
    {
        if (!isset($data['model']) || !isset($data['relatedRoute'])) {
            throw new \Exception('Need to configure "model" as entity class and "relatedRoute" as route to get action');
        }
        $this->model = $data['model'];
        $this->relatedRoute = $data['relatedRoute'];
    }

    /**
     * {@inheritDoc}
     */
    public function getAliasName()
    {
        return 'transactional';
    }

    /**
     * {@inheritDoc}
     */
    public function allowArray()
    {
        return false;
    }
}
