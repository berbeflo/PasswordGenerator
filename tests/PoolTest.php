<?php

declare(strict_types=1);
namespace berbeflo\PasswordGenerator\Test;

use berbeflo\PasswordGenerator\Pool;
use PHPUnit\Framework\TestCase;

class PoolTest extends TestCase
{
    private $pool;

    public function setUp() : void
    {
        $this->pool = new Pool('aaaa');
    }

    public function testCount()
    {
        $this->assertSame(4, \count($this->pool));
        $this->pool->extractFirst();
        $this->assertSame(3, \count($this->pool));
        $this->pool->extractFirst();
        $this->assertSame(2, \count($this->pool));
        $this->pool->extractFirst();
        $this->assertSame(1, \count($this->pool));
        $this->pool->extractFirst();
        $this->assertSame(0, \count($this->pool));
    }

    public function testExceptionOnExtractFirst()
    {
        $this->pool->extractFirst();
        $this->pool->extractFirst();
        $this->pool->extractFirst();
        $this->pool->extractFirst();
        $this->expectException(\BadMethodCallException::class);
        $this->pool->extractFirst();
    }

    public function testExtractFirst()
    {
        $this->assertSame('a', $this->pool->extractFirst());
    }
}
