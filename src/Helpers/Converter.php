<?php

declare(strict_types=1);

namespace Core\Helpers;

class Converter
{
    public static array $bn = ['১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '০'];

    public static array $en = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];

    public static array $bengaliLiterature = [
        'ক', 'খ', 'গ', 'ঘ', 'ঙ',
        'চ', 'ছ', 'জ', 'ঝ', 'ঞ',
        'ট', 'ঠ', 'ড', 'ঢ', 'ণ',
        'ত', 'থ', 'দ', 'ধ', 'ন',
        'প', 'ফ', 'ব', 'ভ', 'ম',
        'য', 'র', 'ল', 'শ', 'ষ', 'স', 'হ',
        'ড়', 'ঢ়', 'য়',
        '০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯',
    ];

    public static array $englishLiterature = [
        'ka', 'kha', 'ga', 'gha', 'uma',
        'cha', 'scha', 'ja', 'jha', 'neo',
        'ta', 'tha', 'da', 'dha', 'n',
        'Ta', 'Tha', 'Da', 'Dha', 'Na',
        'pa', 'pha', 'ba', 'bha', 'ma',
        'ja', 'ra', 'la', 'sha', 'sha', 'sa', 'ha',
        'ra', 'ra', 'ya',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
    ];

    public static function bn2en(string $number): array|string
    {
        return str_replace(self::$bn, self::$en, $number);
    }

    public static function en2bn(string $number): array|string
    {
        return str_replace(self::$en, self::$bn, $number);
    }

    /**
     * Convert a string to camel case.
     */
    public static function toCamelCase(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string))));
    }

    /**
     * Convert a string to snake case.
     */
    public static function toSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    /**
     * Convert a string to kebab case.
     */
    public static function toKebabCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
    }

    /**
     * Convert a string to pascal case.
     */
    public static function toPascalCase(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }

    public static function bnLitToEnLit(string $string): string
    {
        return str_replace(self::$bengaliLiterature, self::$englishLiterature, $string);
    }

    public static function enLitToBnLit(string $string): string
    {
        return str_replace(self::$englishLiterature, self::$bengaliLiterature, $string);
    }
}
