<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\CoreRestBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class Transactional extends ConfigurationAnnotation
{
    const NAME = 'Ecentria\Libraries\CoreRestBundle\Annotation\Transactional';

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
     * Constructor.
     *
     * @param array $data Key-value for properties to be defined in this class.
     * @throws \Exception
     */
    public final function __construct(array $data)
    {
        if (!isset($data['model']) || !isset($data['relatedRoute'])) {
            throw new \Exception('Need to configure "model" as entity class and "relatedRoute" as route to get action');
        }
        $this->model = $data['model'];
        $this->relatedRoute = $data['relatedRoute'];
    }

    /**
     * Returns the alias name for an annotated configuration.
     *
     * @return string
     */
    public function getAliasName()
    {
        return 'transactional';
    }

    /**
     * Returns whether multiple annotations of this type are allowed
     *
     * @return bool
     */
    public function allowArray()
    {
        return false;
    }
}