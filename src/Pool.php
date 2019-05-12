<?php

declare(strict_types=1);
namespace berbeflo\PasswordGenerator;

class Pool implements \Countable
{
    private $chars;

    public function __construct(string $chars)
    {
        $this->chars = \str_shuffle($chars);
    }

    public function extractFirst() : string
    {
        if (\count($this) === 0) {
            throw new \BadMethodCallException();
        }

        $char = $this->chars[0];
        $this->chars = \substr($this->chars, 1);

        return $char;
    }

    public function count() : int
    {
        return \strlen($this->chars);
    }
}
