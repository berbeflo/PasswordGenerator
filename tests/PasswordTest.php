<?php

declare(strict_types=1);
namespace berbeflo\PasswordGenerator\Test;

use berbeflo\PasswordGenerator\Password;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testContainsAll()
    {
        $password = new Password();
        $password
            ->add(Password::POOL_SMALL_LETTERS)
            ->add(Password::POOL_BIG_LETTERS)
            ->add(Password::POOL_NUMBERS)
            ->add(Password::POOL_SPECIAL_CHARS)
            ->require(Password::POOL_SMALL_LETTERS, 1)
            ->require(Password::POOL_BIG_LETTERS, 1)
            ->require(Password::POOL_NUMBERS, 1)
            ->require(Password::POOL_SPECIAL_CHARS, 1);

        $pwString = $password->create(4);

        $this->assertTrue(\preg_match('/[a-z]/', $pwString) === 1);
        $this->assertTrue(\preg_match('/[A-Z]/', $pwString) === 1);
        $this->assertTrue(\preg_match('/[0-9]/', $pwString) === 1);
        $this->assertTrue(\preg_match('/[!$=_*+-]/', $pwString) === 1);
    }

    public function testExceptionOnAdd()
    {
        $password = new Password();
        $this->expectException(\InvalidArgumentException::class);
        $password->add(-1);
    }

    public function testExceptionOnAddAfterCreate()
    {
        $password = new Password();
        $password->add(Password::POOL_SMALL_LETTERS);
        $password->create();
        $this->expectException(\BadMethodCallException::class);
        $password->add(Password::POOL_BIG_LETTERS);
    }

    public function testExceptionOnRequire()
    {
        $password = new Password();
        $this->expectException(\InvalidArgumentException::class);
        $password->require(Password::POOL_SMALL_LETTERS, 1);
    }

    public function testExceptionOnRequireNegative()
    {
        $password = new Password();
        $password->add(Password::POOL_SMALL_LETTERS);
        $this->expectException(\InvalidArgumentException::class);
        $password->require(Password::POOL_SMALL_LETTERS, -1);
    }

    public function testExceptionOnRequireAfterCreate()
    {
        $password = new Password();
        $password->add(Password::POOL_SMALL_LETTERS);
        $password->create();
        $this->expectException(\BadMethodCallException::class);
        $password->require(Password::POOL_SMALL_LETTERS, 1);
    }

    public function testExceptionOnRequireTooMuch()
    {
        $password = new Password();
        $password->add(Password::POOL_NUMBERS);
        $this->expectException(\InvalidArgumentException::class);
        $password->require(Password::POOL_NUMBERS, 30);
    }

    public function testExceptionOnCreateAfterCreate()
    {
        $password = new Password();
        $password->add(Password::POOL_SMALL_LETTERS);
        $password->create();
        $this->expectException(\BadMethodCallException::class);
        $password->create();
    }

    public function testExceptionOnTooLess()
    {
        $password = new Password();
        $password->add(Password::POOL_SMALL_LETTERS);
        $this->expectException(\InvalidArgumentException::class);
        $password->create(-1);
    }

    public function testExceptionOnLessThanRequired()
    {
        $password = new Password();
        $password->add(Password::POOL_SMALL_LETTERS);
        $password->require(Password::POOL_SMALL_LETTERS, 2);
        $this->expectException(\InvalidArgumentException::class);
        $password->create(1);
    }

    public function testCreate()
    {
        $password = new Password();
        $password->add(Password::POOL_SMALL_LETTERS);
        $pwString = $password->create(6);

        $this->assertSame(6, \strlen($pwString));
        $this->assertTrue(\preg_match('/^[a-z]+$/', $pwString) === 1);
    }
}
