<?php

declare(strict_types=1);

namespace App\Utils;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;

use function mb_convert_encoding;
use function mb_detect_encoding;
use function mb_internal_encoding;
use function mb_strlen;
use function mb_strtolower;
use function mb_strtoupper;
use function mb_substr;
use function preg_replace;

final class StringUtils
{
    /**
     * Make first character uppercase
     *
     * @param string      $str     String
     * @param null|string $charset Charset
     *
     * @return string
     */
    public static function mbUcfirst(string $str, null|string $charset = null): string
    {
        if (false === isset($charset)) {
            $charset = mb_internal_encoding();
        }

        $str = mb_strtolower($str, $charset);
        $letter = mb_strtoupper(mb_substr($str, 0, 1, $charset), $charset);
        $suffix = mb_substr($str, 1, mb_strlen($str, $charset) - 1, $charset);

        return $letter . $suffix;
    }

    /**
     * Slug
     *
     * @param string $string    String
     * @param bool   $lowercase Lower-case letters
     * @param string $separator Word separator
     *
     * @return string
     */
    public static function slug(string $string, bool $lowercase = true, string $separator = '-'): string
    {
        return (string) (new AsciiSlugger())->slug($lowercase ? mb_strtolower($string) : $string, $separator);
    }

    /**
     * String to snake case
     *
     * @param string $string String
     *
     * @return string
     */
    public static function stringToSnakeCase(string $string): string
    {
        return (new UnicodeString($string))->snake()->__toString();
    }

    /**
     * Replaces dup spaces with one space.
     *
     * @param string $input
     *
     * @return string
     */
    public static function removeMultipleSpace(string $input): string
    {
        return preg_replace('/\x{20}{2,}/', ' ', $input);
    }

    public static function getStringBetween(string $string, string $start, string $end): null|string
    {
        if (1 === preg_match("/$start(.*?)$end/", $string, $match)) {
            if (count($match) > 1) {
                return $match[1];
            }
        }

        return null;
    }

    /**
     * @param non-empty-string $pattern
     */
    public static function regExpMatch(string $pattern, string $subject): null|array
    {
        if (preg_match($pattern, $subject, $match) == 1) {
            if (count($match) > 0) {
                return $match;
            }
        }

        return null;
    }

    /**
     * Convert encoding
     *
     * @param string[] $fromEncodings
     */
    public static function convertEncoding(
        string $string,
        string $toEncoding = 'UTF-8',
        null|string $fromEncoding = null,
        array $fromEncodings = ['Windows-1251'],
    ): string {
        $fromEncoding = $fromEncoding ?? mb_detect_encoding($string, $fromEncodings, true);

        return mb_convert_encoding($string, $toEncoding, $fromEncoding);
    }
}
