<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\CoreRestBundle\Validator\Constraints;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as BaseUniqueEntity;

/**
 * Unique entity constraint
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class UniqueEntity extends BaseUniqueEntity
{
    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'unique_entity';
    }
}
