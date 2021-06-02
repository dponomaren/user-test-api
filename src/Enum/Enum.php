<?php

namespace App\Enum;

use ReflectionClass;

class Enum implements EnumInterface
{
    /**
     * @return array
     *
     * @throws \ReflectionException
     */
    public static function getConstants(): array
    {
        $reflect = new ReflectionClass(get_called_class());

        return $reflect->getConstants();
    }

    /**
     * @param string $name
     * @param bool   $strict
     *
     * @return bool
     *
     * @throws \ReflectionException
     */
    public static function isValidName(string $name, $strict = false): bool
    {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));

        return in_array(strtolower($name), $keys);
    }

    /**
     * @param mixed $value Value to check
     *
     * @return bool
     *
     * @throws \ReflectionException
     */
    public static function isValidValue($value): bool
    {
        $values = array_values(self::getConstants());

        return in_array($value, $values, true);
    }
}