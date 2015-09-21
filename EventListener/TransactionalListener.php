<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\EventListener;

use Doctrine\Common\Annotations\Reader,
    Doctrine\Common\Util\ClassUtils;

use Doctrine\Common\Persistence\ManagerRegistry;
use Ecentria\Libraries\EcentriaRestBundle\Annotation\AvoidTransaction,
    Ecentria\Libraries\EcentriaRestBundle\Annotation\Transactional,
    Ecentria\Libraries\EcentriaRestBundle\Entity\Transaction,
    Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\TransactionBuilder,
    Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\TransactionResponseManager;

use Ecentria\Libraries\EcentriaRestBundle\Services\Embedded\EmbeddedManager;
use FOS\RestBundle\View\View;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent,
    Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent,
    Symfony\Component\HttpKernel\Event\PostResponseEvent,
    Symfony\Component\HttpKernel\KernelEvents,
    Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The ControllerListener class parses annotation blocks located in
 * controller classes.
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionalListener implements EventSubscriberInterface
{
    /**
     * Reader
     *
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
     * Registry
     *
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * Transaction response manager
     *
     * @var TransactionResponseManager
     */
    private $transactionResponseManager;

    /**
     * Transactional annotation object
     *
     * @var Transactional|null
     */
    private $transactional;

    /**
     * Constructor.
     *
     * @param Reader $reader An Reader instance
     * @param TransactionBuilder $transactionBuilder
     * @param ManagerRegistry $registry
     * @param TransactionResponseManager $transactionResponseManager
     */
    public function __construct(
        Reader $reader,
        TransactionBuilder $transactionBuilder,
        ManagerRegistry $registry,
        TransactionResponseManager $transactionResponseManager
    ) {
        $this->reader = $reader;
        $this->transactionBuilder = $transactionBuilder;
        $this->registry = $registry;
        $this->transactionResponseManager = $transactionResponseManager;
    }

    /**
     * Modifies the Request object to apply configuration information found in
     * controllers annotations like the template to render or HTTP caching
     * configuration.
     *
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     *
     * @return void
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }

        $className = class_exists('Doctrine\Common\Util\ClassUtils') ? ClassUtils::getClass($controller[0]) : get_class($controller[0]);
        $object = new \ReflectionClass($className);

        $this->transactional = $this->reader->getClassAnnotation($object, Transactional::NAME);

        if (!$this->transactional instanceof Transactional) {
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
        $modelName = $this->transactional->model;
        $model = new $modelName();

        $this->transactionBuilder->setRequestMethod($request->getRealMethod());
        $this->transactionBuilder->setRequestSource(Transaction::SOURCE_REST);
        $this->transactionBuilder->setRelatedRoute($this->transactional->relatedRoute);
        $ids = [];
        foreach ($model->getIds() as $field => $value) {
            $ids[$field] = $request->attributes->get($field);
        }
        $this->transactionBuilder->setRelatedIds($ids);
        $this->transactionBuilder->setModel($this->transactional->model);

        $transaction = $this->transactionBuilder->build();

        $request->attributes->set('transaction', $transaction);
    }

    /**
     * Let's process transaction
     *
     * @param GetResponseForControllerResultEvent $event event
     *
     * @return void
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $view = $event->getControllerResult();

        if (!$view instanceof View) {
            return;
        }

        $transaction = $request->get('transaction');

        if ($transaction instanceof Transaction) {
            $data = $view->getData();
            $violations = $request->get('violations');
            $view->setData($this->transactionResponseManager->handle($transaction, $data, $violations));
            if ($this->transactional instanceof Transactional && $this->transactional->writeStatusCodes) {
                $view->setStatusCode($transaction->getStatus());
            }
            if (!$transaction->getSuccess()) {
                $request->attributes->set(
                    EmbeddedManager::KEY_EMBED,
                    EmbeddedManager::GROUP_ALL
                );
            }

        }
    }

    /**
     * On kernel terminate
     *
     * @param PostResponseEvent $postResponseEvent postResponseEvent
     *
     * @return void
     */
    public function onKernelTerminate(PostResponseEvent $postResponseEvent)
    {
        $request = $postResponseEvent->getRequest();
        $transaction = $request->attributes->get('transaction');
        if ($transaction) {
            $className = class_exists('Doctrine\Common\Util\ClassUtils') ? ClassUtils::getClass($transaction) : get_class($transaction);
            $entityManager = $this->registry->getManagerForClass($className);

            $entityManager->clear();
            $entityManager->persist($transaction);
            $entityManager->flush();
        }
    }

    /**
     * Get subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::VIEW       => array('onKernelView', 300),
            KernelEvents::TERMINATE  => 'onKernelTerminate'
        );
    }
}
