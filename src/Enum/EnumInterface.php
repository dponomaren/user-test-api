<?php

namespace App\Enum;

interface EnumInterface
{
    /**
     * @return array
     */
    public static function getConstants(): array;

    /**
     * @param string $name
     * @param bool   $strict
     *
     * @return bool
     */
    public static function isValidName(string $name, $strict = false): bool;

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function isValidValue($value): bool;
}