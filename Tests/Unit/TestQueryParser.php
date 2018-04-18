<?php

namespace MNC\Bundle\RestBundle\Tests\Unit;

use MNC\Bundle\RestBundle\Manager\QueryParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class TestQueryParser extends TestCase
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function setUp()
    {
        $this->requestStack = new RequestStack();
    }

    public function tearDown()
    {
        $this->requestStack = null;
    }

    public function testOnNoQueryParamPresent()
    {
        $parser = $this->createQueryParser();
        $output = $parser->getOrderBy();

        $this->assertSame(null, $output);
    }

    public function testOnBlankQueryParam()
    {
        $parser = $this->createQueryParser(['order' => '']);
        $output = $parser->getOrderBy();

        $this->assertSame(null, $output);
    }

    public function testOrderByValidSingleClause()
    {
        $parser = $this->createQueryParser(['order' => 'field|ASC']);
        $output = $parser->getOrderBy();

        $this->assertArrayHasKey('field', $output);
        $this->assertCount(1, $output);
        $this->assertSame('ASC', $output['field']);
    }

    public function testOrderByValidSingleClauseWithOrderMissing()
    {
        $parser = $this->createQueryParser(['order' => 'field']);
        $output = $parser->getOrderBy();

        $this->assertCount(1, $output);
        $this->assertSame('ASC', $output['field']);
    }

    public function testOrderByValidMultipleClause()
    {
        $parser = $this->createQueryParser(['order' => 'fieldone|ASC,fieldtwo|DESC']);
        $output = $parser->getOrderBy();

        $this->assertCount(2, $output);
        $this->assertArrayHasKey('fieldone', $output);
        $this->assertArrayHasKey('fieldtwo', $output);
        $this->assertSame('ASC', $output['fieldone']);
        $this->assertSame('DESC', $output['fieldtwo']);
    }

    public function testOrderByValidMultipleClauseWithOrderMissing()
    {
        $parser = $this->createQueryParser(['order' => 'fieldone,fieldtwo']);
        $output = $parser->getOrderBy();

        $this->assertCount(2, $output);
        $this->assertArrayHasKey('fieldone', $output);
        $this->assertArrayHasKey('fieldtwo', $output);
        $this->assertSame('ASC', $output['fieldone']);
        $this->assertSame('ASC', $output['fieldtwo']);
    }

    public function testLimitOnEmptyRequest()
    {
        $parser = $this->createQueryParser();
        $output = $parser->getLimit();

        $this->assertInternalType('int', $output);
        $this->assertEquals(20, $output);
    }

    public function testLimit()
    {
        $parser = $this->createQueryParser(['limit' => 10]);
        $output = $parser->getLimit();

        $this->assertInternalType('int', $output);
        $this->assertEquals(10, $output);
    }

    public function testLimitWithSize()
    {
        $parser = $this->createQueryParser(['size' => 40]);
        $output = $parser->getLimit();

        $this->assertInternalType('int', $output);
        $this->assertEquals(40, $output);
    }

    public function testLimitNotNumerical()
    {
        $parser = $this->createQueryParser(['limit' => 'd3jr32r']);
        $output = $parser->getLimit();

        $this->assertInternalType('int', $output);
        $this->assertEquals(20, $output);
    }

    public function testPageToOffsetConverter()
    {
        $parser = $this->createQueryParser();
        $limit = $parser->getLimit();
        $offset = $parser->getOffset();

        $this->assertEquals(20, $limit);
        $this->assertEquals(0, $offset);

        $parser = $this->createQueryParser(['size' => 15, 'page' => 3]);
        $limit = $parser->getLimit();
        $offset = $parser->getOffset();

        $this->assertEquals(15, $limit);
        $this->assertEquals(30, $offset);

        $parser = $this->createQueryParser(['size' => 1, 'page' => 36]);
        $limit = $parser->getLimit();
        $offset = $parser->getOffset();

        $this->assertEquals(1, $limit);
        $this->assertEquals(35, $offset);
    }

    public function testOffsetToPageConverter()
    {
        $parser = $this->createQueryParser();
        $page = $parser->getPage();
        $size = $parser->getSize();

        $this->assertEquals(20, $size);
        $this->assertEquals(1, $page);

        $parser = $this->createQueryParser(['limit' => 15, 'offset' => 30]);
        $page = $parser->getPage();
        $size = $parser->getSize();

        $this->assertEquals(15, $size);
        $this->assertEquals(3, $page);

        $parser = $this->createQueryParser(['limit' => 1, 'offset' => 35]);
        $page = $parser->getPage();
        $size = $parser->getSize();

        $this->assertEquals(1, $size);
        $this->assertEquals(36, $page);
    }

    /**
     * @param array $params
     * @return QueryParser
     */
    private function createQueryParser(array $params = [])
    {
        $request = new Request();
        foreach ($params as $key => $value) {
            $request->query->set($key, $value);
        }
        $this->requestStack->push($request);
        return new QueryParser($this->requestStack);
    }
}
