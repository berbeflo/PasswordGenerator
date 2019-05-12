<?php

declare(strict_types=1);
namespace berbeflo\PasswordGenerator;

class Password
{
    public const OPTION_ALLOW_UNCLEAR_CHARS = 1;
    public const OPTION_ALLOW_DOUBLE_CHARS = 2;

    public const POOL_SMALL_LETTERS = 0;
    public const POOL_BIG_LETTERS = 1;
    public const POOL_NUMBERS = 2;
    public const POOL_SPECIAL_CHARS = 3;

    public const DEFAULT_LENGTH = 12;

    protected $allowUnClearChars = false;
    private $allowDoubleChars = false;

    private $valid = true;
    private $pools = [];
    private $poolItemCreators = [];

    public function __construct(int $options = self::OPTION_ALLOW_DOUBLE_CHARS)
    {
        if ($options === 0) {
            return;
        }

        if ((self::OPTION_ALLOW_UNCLEAR_CHARS & $options) !== 0) {
            $this->allowUnClearChars = true;
        }

        if ((self::OPTION_ALLOW_DOUBLE_CHARS & $options) !== 0) {
            $this->allowDoubleChars = true;
        }

        $this->registerPoolItemCreator(self::POOL_SMALL_LETTERS, [$this, 'getSmallLetters']);
        $this->registerPoolItemCreator(self::POOL_BIG_LETTERS, [$this, 'getBigLetters']);
        $this->registerPoolItemCreator(self::POOL_NUMBERS, [$this, 'getNumbers']);
        $this->registerPoolItemCreator(self::POOL_SPECIAL_CHARS, [$this, 'getSpecialChars']);
    }

    protected function registerPoolItemCreator(int $pool, callable $callable) : void
    {
        $this->poolItemCreators[$pool] = $callable;
    }

    public function add(int $pool) : self
    {
        if (!$this->valid) {
            throw new \BadMethodCallException();
        }

        if (!isset($this->poolItemCreators[$pool])) {
            throw new \InvalidArgumentException();
        }

        $func = $this->poolItemCreators[$pool];
        $items = $func();

        if ($this->allowDoubleChars) {
            $items = \str_repeat($items, 2);
        }
        $this->pools[$pool] = [
            'pool' => new Pool($items),
            'required' => 0,
        ];

        return $this;
    }

    public function require(int $pool, int $amount) : self
    {
        if (!$this->valid) {
            throw new \BadMethodCallException();
        }

        if (!isset($this->pools[$pool])) {
            throw new \InvalidArgumentException();
        }

        if ($amount < 0) {
            throw new \InvalidArgumentException();
        }

        if (\count($this->pools[$pool]['pool']) < $amount) {
            throw new \InvalidArgumentException();
        }

        $this->pools[$pool]['required'] = $amount;

        return $this;
    }

    public function create(int $length = self::DEFAULT_LENGTH) : string
    {
        if (!$this->valid) {
            throw new \BadMethodCallException();
        }

        if ($length < 1) {
            throw new \InvalidArgumentException();
        }

        $password = '';
        $minLength = \array_reduce($this->pools, function (int $carry, array $element) {
            return $carry + $element['required'];
        }, 0);

        if ($minLength > $length) {
            throw new \InvalidArgumentException();
        }

        $this->valid = false;

        foreach ($this->pools as ['pool' => $pool, 'required' => &$required]) {
            while ($required-- > 0) {
                $password .= $pool->extractFirst();
            }
        }

        while (\strlen($password) < $length) {
            $password .= $this->getRandomChar();
        }

        return str_shuffle($password);
    }

    private function getRandomChar() : string
    {
        $validPools = [];

        foreach ($this->pools as ['pool' => $pool]) {
            if (\count($pool) > 0) {
                $validPools[] = $pool;
            }
        }

        \shuffle($validPools);

        return $validPools[0]->extractFirst();
    }

    private function getSmallLetters() : string
    {
        $letters = 'abcdefghjkmnpqrstuvwxyz';

        if ($this->allowUnClearChars) {
            $letters .= 'ilo';
        }

        return $letters;
    }

    private function getBigLetters() : string
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';

        if ($this->allowUnClearChars) {
            $letters .= 'IO';
        }

        return $letters;
    }

    private function getNumbers() : string
    {
        $numbers = '23456789';

        if ($this->allowUnClearChars) {
            $numbers .= '01';
        }

        return $numbers;
    }

    private function getSpecialChars() : string
    {
        $specials = '!$=-_*+';

        if ($this->allowUnClearChars) {
            $specials .= ',.;:';
        }

        return $specials;
    }
}
