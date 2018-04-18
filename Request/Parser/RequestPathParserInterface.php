<?php

namespace MNC\Bundle\RestBundle\Request\Parser;

use MNC\Bundle\RestBundle\Request\Path\PathInfoInterface;

/**
 * Interface RequestPathParserInterface
 * @package MNC\Bundle\RestBundle\Request\Parser
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface RequestPathParserInterface
{
    /**
     * @param $requestPath
     * @return PathInfoInterface
     */
    public function parse(string $requestPath);

    /**
     * Sets the number of blocks to skip
     * @param int $number
     */
    public function setSkippingBlocks(int $number): void;
}