<?php

namespace MNC\Bundle\RestBundle\Doctrine\Utils;

/**
 * @author Amrouche Hamza <hamza.simperfit@gmail.com>
 */
interface QueryNameGeneratorInterface
{
    /**
     * Generates a cacheable alias for DQL join.
     *
     * @param string $association
     *
     * @return string
     */
    public function generateJoinAlias(string $association): string;
    /**
     * Generates a cacheable parameter name for DQL query.
     *
     * @param string $name
     *
     * @return string
     */
    public function generateParameterName(string $name): string;
}