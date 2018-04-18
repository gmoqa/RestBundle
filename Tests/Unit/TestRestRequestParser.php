<?php

namespace MNC\Bundle\RestBundle\Tests\Unit;

use MNC\Bundle\RestBundle\Request\Parser\RestRequestParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RestRequestParserTest
 * @package MNC\Bundle\RestBundle\Tests\Unit
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class TestRestRequestParser extends TestCase
{
    public function testParseWithNormalRequest()
    {
        $request = Request::create('users/1/groups/432532/roles', 'GET');
        $parser = new RestRequestParser();
        $pathInfo = $parser->parse($request->getPathInfo());

        $this->assertEquals(3, $pathInfo->count());
        $this->assertFalse($pathInfo->hasOneBlock());

        $blockOne = $pathInfo->get(0);
        $this->assertEquals('users', $blockOne->getResourceName());
        $this->assertEquals('1', $blockOne->getResourceId());

        $blockTwo = $pathInfo->get(1);
        $this->assertEquals('groups', $blockTwo->getResourceName());
        $this->assertEquals('432532', $blockTwo->getResourceId());

        $blockThree = $pathInfo->get(2);
        $this->assertEquals('roles', $blockThree->getResourceName());
        $this->assertEquals(null, $blockThree->getResourceId());
    }

    public function testParseWithSkipping()
    {
        $request = Request::create('api/v1/users/1/groups/432532/roles', 'GET');
        $parser = new RestRequestParser();
        $parser->setSkippingBlocks(2);
        $pathInfo = $parser->parse($request->getPathInfo());

        $this->assertEquals(3, $pathInfo->count());
        $this->assertFalse($pathInfo->hasOneBlock());

        $blockOne = $pathInfo->get(0);
        $this->assertEquals('users', $blockOne->getResourceName());
        $this->assertEquals('1', $blockOne->getResourceId());

        $blockTwo = $pathInfo->get(1);
        $this->assertEquals('groups', $blockTwo->getResourceName());
        $this->assertEquals('432532', $blockTwo->getResourceId());

        $blockThree = $pathInfo->get(2);
        $this->assertEquals('roles', $blockThree->getResourceName());
        $this->assertEquals(null, $blockThree->getResourceId());
    }

    public function testParseWithQueryParams()
    {
        $request = Request::create('api/v1/users/1/groups/432532/roles?query=3r32223f32&query=345324532', 'GET');
        $parser = new RestRequestParser();
        $parser->setSkippingBlocks(2);
        $pathInfo = $parser->parse($request->getPathInfo());

        $this->assertEquals(3, $pathInfo->count());
        $this->assertFalse($pathInfo->hasOneBlock());

        $blockOne = $pathInfo->get(0);
        $this->assertEquals('users', $blockOne->getResourceName());
        $this->assertEquals('1', $blockOne->getResourceId());

        $blockTwo = $pathInfo->get(1);
        $this->assertEquals('groups', $blockTwo->getResourceName());
        $this->assertEquals('432532', $blockTwo->getResourceId());

        $blockThree = $pathInfo->get(2);
        $this->assertEquals('roles', $blockThree->getResourceName());
        $this->assertEquals(null, $blockThree->getResourceId());
    }

    public function testParseWithOneResource()
    {
        $request = Request::create('api/v1/users', 'GET');
        $parser = new RestRequestParser();
        $parser->setSkippingBlocks(2);
        $pathInfo = $parser->parse($request->getPathInfo());

        $this->assertEquals(1, $pathInfo->count());
        $this->assertTrue($pathInfo->hasOneBlock());

        $blockOne = $pathInfo->get(0);
        $this->assertEquals('users', $blockOne->getResourceName());
        $this->assertEquals(null, $blockOne->getResourceId());
    }

    public function testExceptionInvalidFormat()
    {
        $request = Request::create('api/v1//users', 'GET');
        $parser = new RestRequestParser();
        $parser->setSkippingBlocks(2);
        try {
            $pathInfo = $parser->parse($request->getPathInfo());
        } catch (\RuntimeException $e) {
            $this->assertInstanceOf(\RuntimeException::class, $e);
            $this->assertSame('Invalid structure on request path', $e->getMessage());
        }
    }
}
