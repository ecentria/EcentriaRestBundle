<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Ecentria\Libraries\CoreRestBundle\Annotation\AvoidTransaction;
use Ecentria\Libraries\CoreRestBundle\Annotation\Transactional;
use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use Ecentria\Libraries\CoreRestBundle\Services\Transaction\TransactionBuilder;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Util\ClassUtils;

/**
 * The ControllerListener class parses annotation blocks located in
 * controller classes.
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionalListener implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Transaction builder
     *
     * @var TransactionBuilder
     */
    private $transactionBuilder;

    /**
     * Entity manager
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param Reader $reader An Reader instance
     * @param TransactionBuilder $transactionBuilder
     * @param EntityManager $entityManager
     */
    public function __construct(Reader $reader, TransactionBuilder $transactionBuilder, EntityManager $entityManager)
    {
        $this->reader = $reader;
        $this->transactionBuilder = $transactionBuilder;
        $this->entityManager = $entityManager;
    }

    /**
     * Modifies the Request object to apply configuration information found in
     * controllers annotations like the template to render or HTTP caching
     * configuration.
     *
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {

        if (!is_array($controller = $event->getController())) {
            return;
        }

        $className = class_exists('Doctrine\Common\Util\ClassUtils') ? ClassUtils::getClass($controller[0]) : get_class($controller[0]);
        $object = new \ReflectionClass($className);

        $transactional = $this->reader->getClassAnnotation($object, Transactional::NAME);

        if (!$transactional instanceof Transactional) {
            return;
        }

        $avoidTransaction = $this->reader->getMethodAnnotation(
            $object->getMethod($controller[1]),
            AvoidTransaction::NAME
        );

        if (!is_null($avoidTransaction)) {
            return;
        }

        $request = $event->getRequest();

        $this->transactionBuilder->setRequestMethod($request->getRealMethod());
        $this->transactionBuilder->setRequestSource(Transaction::SOURCE_REST);
        $this->transactionBuilder->setRelatedRoute($transactional->relatedRoute);
        $this->transactionBuilder->setRelatedId($request->get('id'));
        $this->transactionBuilder->setModel($transactional->model);

        $request->attributes->set('transaction', $this->transactionBuilder->build());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::TERMINATE => 'onKernelTerminate'
        );
    }

    /**
     * On kernel terminate
     *
     * @param PostResponseEvent $postResponseEvent
     */
    public function onKernelTerminate(PostResponseEvent $postResponseEvent)
    {
        $request = $postResponseEvent->getRequest();
        if ($request->attributes->get('transaction')) {
            $this->entityManager->flush();
        }
    }
}
