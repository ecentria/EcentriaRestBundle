<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2015, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\EcentriaRestBundle\Exception\CreatorDataTypeException;
use Ecentria\Libraries\EcentriaRestBundle\Model\RequestCreator\RequestCreatorStrategyInterface;
use Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudEntityInterface;
use Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudUnitOfWork;
use Ecentria\Libraries\EcentriaRestBundle\Services\CRUD\CrudManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Router;

/**
 * Creator
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class RequestCreator
{
    /**
     * CrudManager
     *
     * @var CrudManager
     */
    private $crudManager;

    /**
     * Request stack
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Router
     *
     * @var Router
     */
    private $router;

    /**
     * Kernel
     *
     * @var KernelInterface
     */
    private $kernel;

    /**
     * Response
     *
     * @var Response
     */
    private $lastResponse;

    /**
     * Info builder
     *
     * @var InfoBuilder
     */
    private $infoBuilder;

    /**
     * Constructor
     *
     * @param CrudManager     $crudManager  CrudManager
     * @param RequestStack    $requestStack Request stack
     * @param Router          $router       Router
     * @param KernelInterface $kernel       Kernel
     * @param InfoBuilder     $infoBuilder  Info builder
     */
    public function __construct(
        CrudManager $crudManager,
        RequestStack $requestStack,
        Router $router,
        KernelInterface $kernel,
        InfoBuilder $infoBuilder
    ) {
        $this->crudManager = $crudManager;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->kernel = $kernel;
        $this->infoBuilder = $infoBuilder;
    }

    /**
     * Apply recipe
     *
     * @param RequestCreatorStrategyInterface $strategy Strategy
     * @param mixed                           $data     Data
     *
     * @return bool
     */
    public function applyStrategy(RequestCreatorStrategyInterface $strategy, $data)
    {
        $collection = $this->normalize($data);
        return $strategy->apply($this, $collection);
    }

    /**
     * Add info message
     *
     * @param string $key     Key
     * @param string $message Message
     * @return void
     */
    public function addInfoMessage($key, $message)
    {
        $this->infoBuilder->addMessage($key, $message);
    }

    /**
     * normalize
     *
     * @param mixed $data Data
     * @throws CreatorDataTypeException
     *
     * @return ArrayCollection
     */
    private function normalize($data)
    {
        if ($data instanceof ArrayCollection) {
            $collection = $data;
        } elseif (is_array($data)) {
            $collection = new ArrayCollection($data);
        } else {
            $collection = new ArrayCollection();
            $collection->add($data);
        }

        foreach ($collection as $item) {
            if (!$item instanceof CrudEntityInterface) {
                throw new CreatorDataTypeException('Collection items should implement CrudEntityInterface');
            }
        }

        return $collection;
    }

    /**
     * Getter
     *
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * ClearTransaction
     *
     * @return void
     */
    public function clearTransaction()
    {
        $this->requestStack->getMasterRequest()->attributes->remove('transaction');
    }

    /**
     * Post
     *
     * @param string          $route      Route
     * @param ArrayCollection $collection Collection
     *
     * @return bool
     */
    public function post($route, ArrayCollection $collection)
    {
        if ($collection->count()) {
            $response = $this->doRequest($route, $collection);
            if ($response->getStatusCode() != 201) {
                $this->lastResponse = $response;
                return false;
            }
        }
        return true;
    }

    /**
     * Refresh
     *
     * @param mixed &$data data
     *
     * @return void
     */
    public function refresh(&$data)
    {
        if ($data instanceof ArrayCollection) {
            foreach ($data as $item) {
                $this->crudManager->refresh($item, $item->getPrimaryKey());
            }
        } else {
            $this->crudManager->refresh($data, $data->getPrimaryKey());
        }
    }

    /**
     * GetUnitOfWork
     *
     * @param ArrayCollection $collection         Collection
     * @param bool            $clearEntityManager ClearEntityManager
     *
     * @return CrudUnitOfWork
     */
    public function getUnitOfWork(ArrayCollection $collection, $clearEntityManager = false)
    {
        if ($clearEntityManager) {
            $this->crudManager->clearEntityManager();
        }
        return $this->crudManager->filterCollection($collection, false);
    }

    /**
     * Patch
     *
     * @param string $route Route
     * @param array  $data  Data
     * @param mixed  $id    Id
     *
     * @return Response
     */
    public function patch($route, array $data, $id)
    {
        $this->lastResponse = $this->doRequest($route, array($data), 'PATCH', ['id' => $id]);
        return $this->lastResponse;
    }

    /**
     * Put
     *
     * @param string $route Route
     * @param array  $data  Data
     * @param mixed  $id    Id
     *
     * @return Response
     */
    public function put($route, array $data, $id)
    {
        $this->lastResponse = $this->doRequest($route, array($data), 'PUT', ['id' => $id]);
        return $this->lastResponse;
    }

    /**
     * Do request
     *
     * @param string $route      route
     * @param mixed  $content    content
     * @param string $method     method
     * @param array  $parameters Parameters
     *
     * @return Response
     */
    private function doRequest($route, $content, $method = 'POST', $parameters = [])
    {
        if ($content instanceof ArrayCollection) {
            $content = $this->collectionToArray($content);
        }
        $master = $this->requestStack->getMasterRequest();
        $uri = $this->router->generate($route, $parameters, Router::ABSOLUTE_URL);
        $request = $master->create($uri, $method, [], [], [], $master->server->all(), json_encode($content));
        return $this->kernel->handle($request, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Collection to array
     *
     * @param ArrayCollection $collection collection
     * @return array
     */
    private function collectionToArray(ArrayCollection $collection)
    {
        $array = [];
        foreach ($collection as $item) {
            $array[] = $item->toArray();
        }
        return $array;
    }
}
