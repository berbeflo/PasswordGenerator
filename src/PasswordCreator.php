<?php

declare(strict_types=1);
namespace berbeflo\PasswordGenerator;

class PasswordCreator
{
    public static function create() : string
    {
        $password = new Password(0);
        $password
            ->add(Password::POOL_SPECIAL_CHARS)
            ->add(Password::POOL_BIG_LETTERS)
            ->add(Password::POOL_SMALL_LETTERS)
            ->add(Password::POOL_NUMBERS)
            ->require(Password::POOL_NUMBERS, 2)
            ->require(Password::POOL_SMALL_LETTERS, 2)
            ->require(Password::POOL_BIG_LETTERS, 2)
            ->require(Password::POOL_SPECIAL_CHARS, 1);

        return $password->create();
    }

    public static function createAlphaNumeric(int $length) : string
    {
        $password = new Password(Password::OPTION_ALLOW_UNCLEAR_CHARS | Password::OPTION_ALLOW_DOUBLE_CHARS);
        $password
            ->add(Password::POOL_BIG_LETTERS)
            ->add(Password::POOL_SMALL_LETTERS)
            ->add(Password::POOL_NUMBERS)
            ->require(Password::POOL_NUMBERS, 1)
            ->require(Password::POOL_SMALL_LETTERS, 1)
            ->require(Password::POOL_BIG_LETTERS, 1);

        return $password->create($length);
    }
}
