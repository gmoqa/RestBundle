<?php

namespace MNC\Bundle\RestBundle\Tests\Unit;

use MNC\Bundle\RestBundle\Resource\Metadata\Factory\ResourceMetadataFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class TestResourceMappingInformation
 * @package MNC\Bundle\RestBundle\Tests\Unit
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class TestResourceMetadataFactory extends TestCase
{

    private $mappings;

    public function setUp()
    {
        $this->mappings = [
            'users' => [
                'class' => 'App\Entity\User',
                'uri_id' => 'id',
                'persistence' => 'doctrine'
            ],
            'churches' => [
                'class' => 'App\Entity\Church',
                'uri_id' => 'id',
                'persistence' => 'doctrine'
            ]
        ];
    }

    public function testGetResourceMetadata()
    {
        $mappingInfo = new ResourceMetadataFactory($this->mappings);

        $meta = $mappingInfo->getResourceMetadata('users');

        $this->assertSame('users', $meta->getUriPathName());
        $this->assertSame('id', $meta->getIdentifierFieldName());
        $this->assertSame('doctrine', $meta->getPersistence());

        $meta = $mappingInfo->getResourceMetadata('churches');

        $this->assertSame('churches', $meta->getUriPathName());
        $this->assertSame('id', $meta->getIdentifierFieldName());
        $this->assertSame('doctrine', $meta->getPersistence());
    }
}
