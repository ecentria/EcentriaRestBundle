<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\CoreRestBundle\Event;

/**
 * Events
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
final class Events
{
    /**
     * Event that will be thrown before creating entity
     */
    const PRE_CREATE = 'ecentria.crud.pre_create';

    /**
     * Event that will be thrown after creating entity
     * This does not imply that the entity is flushed.
     */
    const POST_CREATE = 'ecentria.crud.post_create';

    /**
     * Event that will be thrown before updating entity
     */
    const PRE_UPDATE = 'ecentria.crud.pre_update';

    /**
     * Event that will be thrown after updating entity
     * This implies that the entity is flushed.
     */
    const POST_UPDATE = 'ecentria.crud.post_update';
}
