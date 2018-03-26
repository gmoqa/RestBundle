<?php

namespace MNC\Bundle\RestBundle\Fractalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\QueryBuilder;
use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use MNC\Bundle\RestBundle\Event\PreFetchCollectionEvent;
use MNC\Bundle\RestBundle\MNCRestBundleEvents;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineCollectionAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class Fractalizer
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Manager
     */
    private $manager;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Fractalizer constructor.
     * @param RequestStack             $requestStack
     * @param RouterInterface          $router
     * @param Manager                  $manager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        RequestStack $requestStack,
        RouterInterface $router,
        Manager $manager,
        EventDispatcherInterface $dispatcher
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->manager = $manager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param                     $data
     * @param TransformerAbstract $transformer
     * @param null                $resourceKey
     * @return array
     * @throws \Exception
     */
    public function fractalize($data, TransformerAbstract $transformer, $resourceKey = null)
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->has('with')) {
            $this->manager->parseIncludes($request->query->get('with'));
            if ($data instanceof QueryBuilder) {
                $data = $this->eagerLoadWith($data, $this->manager->getRequestedIncludes());
            }
        }

        if ($this->isPluralResponse($data)) {

            if ($data instanceof QueryBuilder) {
                $event = $this->dispatcher
                    ->dispatch(
                        MNCRestBundleEvents::PRE_FETCH_COLLECTION,
                        new PreFetchCollectionEvent($data, $request)
                    );

                $data = $event->getQuery();
            }

            $paginator = $this->instantiatePaginator($data);
            $results = $paginator->getPaginator()->getCurrentPageResults();

            $resource = new Collection($results, $transformer, $resourceKey);
            $resource->setPaginator($paginator);

        } else {
            $resource = new Item($data, $transformer, $resourceKey);
        }

        $array = array_reverse($this->manager->createData($resource)->toArray(), true);
        if ($request->attributes->has('_filters')) {
            $array['meta'] = array_merge($array['meta'], $request->attributes->get('_filters'));
        }

        return $array;
    }

    /**
     * Returns an instance of the paginator
     * @param $data
     * @return PagerfantaPaginatorAdapter
     * @throws \Exception
     */
    private function instantiatePaginator($data)
    {
        $request = $this->requestStack->getCurrentRequest();
        $router = $this->router;

        $adapter = $this->instantiateAdapter($data);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($request->query->getInt('size') ?: 10);
        $pagerfanta->setCurrentPage($request->query->getInt('page') ?: 1);

        $paginator = new PagerfantaPaginatorAdapter($pagerfanta, function($page) use ($request, $router) {
            $route = $request->attributes->get('_route');
            $params = $request->attributes->get('_route_params');
            $newParams = array_merge($params, $request->query->all());
            $newParams['page'] = $page;
            return $router->generate($route, $newParams);
        });

        return $paginator;
    }

    /**
     * This method tries to guess if data is a collection of items or just a single
     * resource.
     * @param $data
     * @return bool
     */
    private function isPluralResponse($data)
    {
        if ($data instanceof QueryBuilder OR $data instanceof ArrayCollection OR $data instanceof PersistentCollection) {
            return true;
        } elseif (is_array($data) AND sizeof($data) > 0 AND is_object($data[0])) {
            return true;
        } elseif (is_object($data)) {
            return false;
        }
        return false;
    }

    /**
     * @param $data
     * @return ArrayAdapter|DoctrineCollectionAdapter|DoctrineORMAdapter
     * @throws \Exception
     */
    private function instantiateAdapter($data)
    {
        if ($data instanceof QueryBuilder) {
            return new DoctrineORMAdapter($data, false);
        }

        if ($data instanceof ArrayCollection OR $data instanceof PersistentCollection) {
            return new DoctrineCollectionAdapter($data);
        }

        if (is_array($data)) {
            return new ArrayAdapter($data);
        }

        throw FractalizerException::adapterNotFound();
    }

    /**
     * Checks the with query param and eager loads the relationships.
     * @param QueryBuilder $query
     * @param array        $includes
     * @return QueryBuilder
     * TODO: Create a method to parse the includes
     * The includes come in array like this:
     *  - property1.subproperty1
     *  - property1
     *  - property2
     * I should loop over.
     */
    private function eagerLoadWith(QueryBuilder $query, array $includes)
    {
        $entities = $query->getRootEntities();
        foreach ($entities as $entity) {
            // Blank
        }
        // Function that parses the includes
        return $query;
    }
}