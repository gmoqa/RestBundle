<?php

namespace MNC\Bundle\RestBundle\Manager;

/**
 * A container for all the resource managers to ease dependency injection.
 * @package MNC\Bundle\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class ResourceManagerFactory implements ResourceManagerFactoryInterface
{
    /**
     * @var ResourceManagerInterface[]
     */
    private $resourceManagers  = [];

    /**
     * ResourceManagerFactory constructor.
     * @param array|null $resourceManagers
     */
    public function __construct(array $resourceManagers = null)
    {
        $this->resourceManagers = $resourceManagers;
    }

    /**
     * @param $id
     * @return ResourceManagerInterface
     * @throws ResourceManagerFactoryException When manager id does not exist
     */
    public function get($id): ResourceManagerInterface
    {
        if ($this->has($id)) {
            return $this->resourceManagers[$id];
        }
        throw ResourceManagerFactoryException::managerIdDoesNotExist($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function has($id): bool
    {
        return isset($this->resourceManagers[$id]) || array_key_exists($id, $this->resourceManagers);
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->resourceManagers);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->resourceManagers);
    }
}