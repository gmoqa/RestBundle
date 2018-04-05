<?php

namespace MNC\Bundle\RestBundle\Event;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class PreFetchCollectionEvent extends Event
{
    /**
     * @var QueryBuilder
     */
    private $query;
    /**
     * @var Request
     */
    private $request;

    public function __construct(QueryBuilder $query, Request $request)
    {
        $this->query = $query;
        $this->request = $request;
    }

    /**
     * @return QueryBuilder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param QueryBuilder $query
     * @return PreFetchCollectionEvent
     */
    public function setQuery(QueryBuilder $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}