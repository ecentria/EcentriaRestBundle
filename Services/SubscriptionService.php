<?php
/*
 * This file is part of the OpCart software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\CoreRestBundle\Services;

use Ecentria\Libraries\CoreRestBundle\Entity\Repository\SubscriptionRepository;
use Ecentria\Libraries\CoreRestBundle\Entity\Subscription;

/**
 * Subscription create listener
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class SubscriptionService
{
    /**
     * Create & Read & Update & Delete Manager
     *
     * @var CRUDManager
     */
    protected $crudManager;

    /**
     * Subscription repository
     *
     * @var SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * Constructor
     *
     * @param CRUDManager $crudManager
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(CRUDManager $crudManager, SubscriptionRepository $subscriptionRepository)
    {
        $this->crudManager = $crudManager;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Process parent channel's subscription
     *
     * @param Subscription $original
     * @return Subscription|null
     */
    public function processParentSubscription(Subscription $original)
    {
        if ($this->crudManager->validate($original) !== true) {
            return null;
        }
        $channel = $original->getChannel()->getParent();
        if (is_null($channel)) {
            return null;
        }
        $contact = $original->getContact();
        $subscription = $this->subscriptionRepository->findOneBy(array(
            'Contact' => $contact,
            'Channel' => $channel
        ));
        if (is_null($subscription)) {
            $subscription = clone $original;
            $subscription->setChannel($channel);
            $subscription->setContact($contact);
            $this->crudManager->create($subscription);
        } elseif ($subscription instanceof Subscription) {
            if ($original->getStatus() === Subscription::STATUS_EXPLICITLY_SUBSCRIBED) {
                $this->crudManager->setData(
                    $subscription,
                    array(array('status' => Subscription::STATUS_EXPLICITLY_SUBSCRIBED))
                );
                $this->crudManager->update($subscription);
            }
        }
    }
}
