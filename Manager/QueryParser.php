<?php

namespace MNC\Bundle\RestBundle\Manager;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class QueryParser
 * @package MNC\Bundle\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class QueryParser
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * QueryParser constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return array|null
     */
    public function getOrderBy()
    {
        $orderBy = [];
        $rawParam = $this->get('order');
        if (empty($rawParam)) {
            return null;
        }
        // First, we determine if we are facing multiple order by clauses
        $clauses = explode(',', $rawParam);
        // Then, we process the stuff one by one.
        foreach ($clauses as $clause) {
            $array = explode('|', $clause);
            $field = $array[0] ?? null;
            $order = $array[1] ?? 'ASC';
            if (empty($field)) {
                continue;
            }
            $orderBy[$field] = $order;
        }
        if (empty($orderBy)) {
            return null;
        }
        return $orderBy;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        // If we have the limit, we just return it
        $value = $this->evalAndGet('limit');
        if ($value !== null) {
            return $value;
        }

        // Same with the size
        $value = $this->evalAndGet('size');
        if ($value !== null) {
            return $value;
        }

        // If not, we just go with the default
        return 20;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        // If we have the size, we just return it
        $value = $this->evalAndGet('size');
        if ($value !== null) {
            return $value;
        }

        // Same with the limit
        $value = $this->evalAndGet('limit');
        if ($value !== null) {
            return $value;
        }

        // If none, we just return default
        return 20;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        // If we have the offset, we just return it.
        $value = $this->evalAndGet('offset');
        if ($value !== null) {
            return $value;
        }

        // If we have the page, then we need to convert that to offset
        $value = $this->evalAndGet('page');
        if ($value !== null) {
            return ($this->getSize() * $value) - $this->getSize();
        }

        // If none of the above, we just return the default.
        return 0;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        // If we have the page, we just return it.
        $value = $this->evalAndGet('page');
        if ($value !== null) {
            return $value;
        }

        // If we have the offset, then we need to convert that to page
        $value = $this->evalAndGet('offset');
        if ($value !== null) {
            return ($value / $this->getSize()) + 1;
        }

        // If none of the above, we just return the default.
        return 1;
    }

    /**
     * @param string $paramName
     * @return mixed
     */
    private function get(string $paramName)
    {
        return $this->requestStack->getCurrentRequest()->query->get($paramName);
    }

    /**
     * @param string $paramName
     * @return bool
     */
    private function has(string $paramName)
    {
        return $this->requestStack->getCurrentRequest()->query->has($paramName);
    }

    /**
     * @param string $paramName
     * @return int|null
     */
    private function evalAndGet(string $paramName)
    {
        if ($this->has($paramName) AND !empty($this->get($paramName))) {
            $number = (int) $this->get($paramName);
            if (!empty($number)) {
                return $number;
            }
        }
        return null;
    }
}