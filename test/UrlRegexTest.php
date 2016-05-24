<?php

namespace Bravesheep\FlysystemUrlBundle\Test;

use Bravesheep\FlysystemUrlBundle\Resolver\Decoder;

class UrlRegexTest extends AbstractTestCase
{
    public function testBasic()
    {
        $res = Decoder::matchUrl('ftp://example.com');
        $this->assertSame([
            'scheme' => 'ftp',
            'user' => null,
            'pass' => null,
            'host' => 'example.com',
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null
        ], $res);
    }

    public function testBasicUserPass()
    {
        $res = Decoder::matchUrl('ftp://user:pass@example.com');
        $this->assertSame([
            'scheme' => 'ftp',
            'user' => 'user',
            'pass' => 'pass',
            'host' => 'example.com',
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null
        ], $res);
    }

    public function testOnlyUser()
    {
        $res = Decoder::matchUrl('ftp://user@example.com');
        $this->assertSame([
            'scheme' => 'ftp',
            'user' => 'user',
            'pass' => null,
            'host' => 'example.com',
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null
        ], $res);
    }

    public function testUserPassWithPort()
    {
        $res = Decoder::matchUrl('ftp://user:pass@example.com:21');
        $this->assertSame([
            'scheme' => 'ftp',
            'user' => 'user',
            'pass' => 'pass',
            'host' => 'example.com',
            'port' => 21,
            'path' => null,
            'query' => null,
            'fragment' => null
        ], $res);
    }

    public function testPort()
    {
        $res = Decoder::matchUrl('ftp://example.com:21');
        $this->assertSame([
            'scheme' => 'ftp',
            'user' => null,
            'pass' => null,
            'host' => 'example.com',
            'port' => 21,
            'path' => null,
            'query' => null,
            'fragment' => null
        ], $res);
    }

    public function testUserWithAt()
    {
        $res = Decoder::matchUrl('ftp://user@example:pass@example.com');
        $this->assertSame([
            'scheme' => 'ftp',
            'user' => 'user@example',
            'pass' => 'pass',
            'host' => 'example.com',
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null
        ], $res);
    }

    public function testFullWithWeirdPath()
    {
        $res = Decoder::matchUrl('ftp://user@example:p/w@example.com:21/some/path/to/some?example=true@example.com');
        $this->assertSame([
            'scheme' => 'ftp',
            'user' => 'user@example',
            'pass' => 'p/w',
            'host' => 'example.com',
            'port' => 21,
            'path' => '/some/path/to/some',
            'query' => 'example=true@example.com',
            'fragment' => null
        ], $res);
    }
}
