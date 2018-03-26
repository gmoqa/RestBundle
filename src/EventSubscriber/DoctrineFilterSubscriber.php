<?php

namespace MNC\Bundle\RestBundle\EventSubscriber;

use MNC\Bundle\RestBundle\Doctrine\Utils\QueryNameGeneratorInterface;
use MNC\Bundle\RestBundle\DoctrineFilter\DoctrineFilterInterface;
use MNC\Bundle\RestBundle\Event\PreFetchCollectionEvent;
use MNC\Bundle\RestBundle\MNCRestBundleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DoctrineFilterSubscriber
 * @package MNC\Bundle\RestBundle\EventSubscriber
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class DoctrineFilterSubscriber implements EventSubscriberInterface
{
    /**
     * @var DoctrineFilterInterface[]
     */
    private $filters;
    /**
     * @var QueryNameGeneratorInterface
     */
    private $generator;

    public function __construct(QueryNameGeneratorInterface $generator, array $filters = [])
    {
        $this->filters = $filters;
        $this->generator = $generator;
    }

    public static function getSubscribedEvents()
    {
        return [
            MNCRestBundleEvents::PRE_FETCH_COLLECTION => 'onPreFetchCollection'
        ];
    }

    public function onPreFetchCollection(PreFetchCollectionEvent $event)
    {
        $query = $event->getQuery();
        $request = $event->getRequest();
        $resourceClass = $query->getRootEntities()[0];
        $rootAlias = $query->getRootAliases()[0];

        foreach ($this->filters as $filter) {
            if ($filter->supports($request, $resourceClass) !== true) {
                continue;
            }

            $value = $filter->getParamValue($request);

            if ($value === null OR $value === '') {
                continue;
            }

            $array = $filter->getNormalizedFilter($value);

            // We put the filter in the request object.
            if ($request->attributes->has('_filters')) {
                $filters = $request->attributes->get('_filters');
                $filters[$filter->getFilterName()] = $array;
                $newFilters = $filters;
            } else {
                $newFilters[$filter->getFilterName()] = $array;
            }
            $request->attributes->set('_filters', $newFilters);

            // Then, we get the expression:
            $expression = $filter->getExpression($array, $rootAlias);

            $filter->filter($query, $this->generator, $expression);
        }
    }

}