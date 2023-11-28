<?php

declare(strict_types=1);

namespace App\Service\Common;

use App\Common\CommonDef;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use IntlDateFormatter;

use function trim;
use function preg_match;

/**
 * DateTime Service
 */
class DateTimeService
{
    public const DATE_TIME_FORMAT_PATTERNS = [
        'Y'           => '/^[0-9]{4}$/',
        'Y-m'         => '/^[0-9]{4}(-|\/)([1-9]|0[1-9]|1[0-2])$/',
        'Y-m-d'       => '/^[0-9]{4}(-|\/)([1-9]|0[1-9]|1[0-2])(-|\/)([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/',
        'Y-m-d H'     => '/^[0-9]{4}(-|\/)([1-9]|0[1-9]|1[0-2])(-|\/)([1-9]|0[1-9]|[1-2][0-9]|3[0-1])\s(0|[0-1][0-9]|2[0-4])$/', // phpcs:ignore Generic.Files.LineLength.TooLong
        'Y-m-d H:i'   => '/^[0-9]{4}(-|\/)([1-9]|0[1-9]|1[0-2])(-|\/)([1-9]|0[1-9]|[1-2][0-9]|3[0-1])\s(0|[0-1][0-9]|2[0-4]):?(0|[0-5][0-9]|60)$/', // phpcs:ignore Generic.Files.LineLength.TooLong
        'Y-m-d H:i:s' => '/^[0-9]{4}(-|\/)([1-9]|0[1-9]|1[0-2])(-|\/)([1-9]|0[1-9]|[1-2][0-9]|3[0-1])\s(0|[0-1][0-9]|2[0-4]):?((0|[0-5][0-9]):?(0|[0-5][0-9])|6000|60:00)$/', // phpcs:ignore Generic.Files.LineLength.TooLong
    ];

    /**
     * Get full date
     *
     * @param null|DateTimeInterface $date Date
     *
     * @return string
     */
    public static function dateLong(null|DateTimeInterface $date): string
    {
        if (null === $date) {
            $date = new DateTimeImmutable();
        }

        $intlFormatter = new IntlDateFormatter('ru_RU', IntlDateFormatter::LONG, IntlDateFormatter::LONG);
        $intlFormatter->setPattern("\u{00ab}dd\u{00bb} MMMM Y г.");

        return $intlFormatter->format($date);
    }

    public static function getW3c(null|DateTimeInterface $date = null): string
    {
        return ($date ?? static::getCurrentDateTime())->format(DateTimeInterface::W3C);
    }

    public static function getCurrentDateAsString(
        null|DateTimeInterface $date = null,
        string $format = CommonDef::API_DATE_FORMAT,
    ): string {
        return ($date ?? static::getCurrentDateTime())->format($format);
    }

    public static function getCurrentDateTime(): DateTimeInterface
    {
        return new DateTimeImmutable();
    }

    /**
     * Create DateTime from string
     *
     * @param null|string $dateTimeString Date time
     *
     * @return null|DateTimeImmutable
     */
    public static function createDateTimeFromString(
        null|string $dateTimeString,
        string $format = CommonDef::API_DATE_TIME_FORMAT,
    ): null|DateTimeImmutable {
        if (null === $dateTimeString) {
            return null;
        }

        if (empty(trim($dateTimeString))) {
            return null;
        }

        $dateTime = trim($dateTimeString);

        $formatFromPatterns = null;
        foreach (self::DATE_TIME_FORMAT_PATTERNS as $newFormat => $pattern) {
            if (preg_match($pattern, $dateTimeString)) {
                $formatFromPatterns = $newFormat;
            }
        }

        if (preg_match(self::DATE_TIME_FORMAT_PATTERNS['Y-m-d'], $dateTimeString)) {
            $formatFromPatterns = 'Y-m-d|';
        }

        $format = $formatFromPatterns ?? $format;

        $result = ($dateTime) ? DateTimeImmutable::createFromFormat($format, $dateTime) : null;

        if (!$result) {
            return null;
        }

        return $result;
    }

    /**
     * Create string from DateTime
     *
     * @param null|DateTimeInterface $dateTime Date time
     * @param bool                   $utc      Set UTC (GMT) timezone?
     *
     * @return null|string
     */
    public static function createStringFromDateTime(
        null|DateTimeInterface $dateTime,
        string $format = CommonDef::DATE_TIME_FORMAT,
        bool $utc = false,
    ): null|string {
        if (null === $dateTime) {
            return null;
        }

        if ($utc) {
            $dateTime = (DateTimeImmutable::createFromInterface($dateTime))->setTimezone(new DateTimeZone('UTC'));
        }

        return $dateTime->format($format);
    }
}
