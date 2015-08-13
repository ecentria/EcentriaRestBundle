<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Event;

/**
 * Events
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
final class Events
{
    /**
     * Event that will be thrown before creating entities collection
     */
    const COLLECTION_PRE_CREATE = 'ecentria.api.event.crud.collection.pre_create';

    /**
     * Event that will be thrown before creating entity
     */
    const PRE_CREATE = 'ecentria.api.event.crud.pre_create';

    /**
     * Event that will be thrown after creating entity
     * This does not imply that the entity is flushed.
     */
    const POST_CREATE = 'ecentria.api.event.crud.post_create';

    /**
     * Event that will be thrown before updating entity
     */
    const PRE_UPDATE = 'ecentria.api.event.crud.pre_update';

    /**
     * Event that will be thrown after updating entity
     * This implies that the entity is flushed.
     */
    const POST_UPDATE = 'ecentria.api.event.crud.post_update';

    /**
     * Event that will be thrown status api call is fired
     */
    const STATUS_CHECK = 'ecentria.api.status_check';
}
