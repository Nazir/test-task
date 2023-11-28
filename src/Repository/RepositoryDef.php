<?php

declare(strict_types=1);

namespace App\Repository;

use App\Common\CommonDef;
use App\Entity\EntityDef;
use App\Exception as Except;
use App\Service\Common\DateTimeService;
use DateTimeInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;

/**
 * Repository definition
 */
final class RepositoryDef
{
    /**
     * Criteria for filtering Selectable collections.
     */
    public const CRITERIA_ASC = Criteria::ASC;
    public const CRITERIA_DESC = Criteria::DESC;

    /** Filter list */
    public const FILTER_LIST = 'FILTER_LIST';
    public const FILTER_UUID = 'FILTER_UUID';
    public const FILTER_PERIOD = 'FILTER_PERIOD';
    public const FILTER_PERIOD_DATETIME = 'FILTER_PERIOD_DATETIME';
    public const FILTER_RANGE = 'FILTER_RANGE';
    public const FILTER_RANGE_IN_KILOBYTES = 'FILTER_RANGE_IN_KILOBYTES';
    public const FILTER_CUSTOM = 'FILTER_CUSTOM';
    public const FILTER_LIST_AND = 'FILTER_LIST_AND';
    public const FILTER_EXTRA = 'FILTER_EXTRA';

    /**
     * Add filters
     *
     * @param non-empty-array<array-key, string|array<string|int>> $filters
     * @param non-empty-array<array-key, array{type: string, name: string}> $filtersMap
     *
     */
    public static function addFilters(QueryBuilder &$qb, array|null $filters, array $filtersMap): void
    {
        if (empty($filters)) {
            return;
        }

        foreach ($filters as $filterName => $filterValue) {
            if (!isset($filtersMap[$filterName])) {
                throw new Except\ValidationException("Фильтр {$filterName} отсутствует.");
            }
            $paramType = $filtersMap[$filterName]['type'] ?? Types::STRING;

            if ($paramType == RepositoryDef::FILTER_EXTRA) {
                continue;
            }

            $columnName = $filtersMap[$filterName]['name'];

            $defaultParameter = true;
            $andWhere = $qb->expr()->eq($columnName, ':' . $filterName);
            switch ($paramType) {
                case self::FILTER_CUSTOM:
                    $andWhere = null;
                    break;
                case self::FILTER_LIST_AND:
                    if (is_array($filterValue)) {
                        $whereOr = '';
                        $countParam = 0;
                        foreach ($filterValue as $value) {
                            if (!empty($whereOr)) {
                                $whereOr .= " AND ";
                            }
                            $whereOr .= $qb->expr()->andX("{$columnName} = :{$paramType}_{$countParam}");
                            $qb->setParameter("{$paramType}_{$countParam}", $value);
                            ++$countParam;
                        }
                        $andWhere = $qb->expr()->andX($whereOr);
                    }
                    break;
                case Types::STRING:
                    if (is_string($filterValue)) {
                        $filterValue = '%' . mb_strtolower($filterValue, 'UTF-8') . '%';
                        $andWhere = $qb->expr()->like("LOWER({$columnName})", ':' . $filterName);
                    }
                    break;
                case Types::DATE_MUTABLE:
                case Types::DATE_IMMUTABLE:
                case Types::DATETIME_MUTABLE:
                case Types::DATETIME_IMMUTABLE:
                case Types::DATETIMETZ_MUTABLE:
                case Types::DATETIMETZ_IMMUTABLE:
                    if (is_string($filterValue)) {
                        $filterValue = DateTimeService::createDateTimeFromString(dateTimeString: $filterValue);
                    }
                    break;
                case self::FILTER_PERIOD:
                    $defaultParameter = false;
                    /** @var DateTimeInterface|string $periodStart */
                    if (is_array($filterValue)) {
                        /** @var DateTimeInterface|string $periodStart */
                        $periodStart = $filterValue['start'];
                        $periodStart = is_string($periodStart) ?
                            DateTimeService::createDateTimeFromString(dateTimeString: $periodStart) : $periodStart;
                        /** @var DateTimeInterface|string $periodEnd */
                        $periodEnd = $filterValue['end'];
                        $periodEnd = is_string($periodEnd) ?
                            DateTimeService::createDateTimeFromString(dateTimeString: $periodEnd) : $periodEnd;
                        $andWhere = $qb->expr()->between(
                            $columnName,
                            ':' . $filterName . 'Start',
                            ':' . $filterName . 'End',
                        );
                        $qb->setParameter($filterName . 'Start', $periodStart, Types::DATE_MUTABLE);
                        $qb->setParameter($filterName . 'End', $periodEnd, Types::DATE_MUTABLE);
                    }
                    break;
                case RepositoryDef::FILTER_PERIOD_DATETIME:
                    if (!is_array($filterValue)) {
                        break;
                    }
                    $where = new Expr\Andx();
                    if (isset($filterValue['start'])) {
                        /** @var string|DateTimeInterface $start */
                        $start = $filterValue['start'];
                        $start = $start instanceof DateTimeInterface ?
                            DateTimeService::createStringFromDateTime(
                                dateTime: $start,
                                format: CommonDef::API_DATE_FORMAT,
                            ) : $start;
                        if (null === $start) {
                            break;
                        }
                        $start .= ' 00:00:00';
                        $filterNameStart = $filterName . 'Start';
                        $qb->setParameter(key: $filterNameStart, value: $start);
                        $where->add("{$columnName} >= :{$filterNameStart}");
                    }
                    if (isset($filterValue['end'])) {
                        /** @var string|DateTimeInterface $end */
                        $end = $filterValue['end'];
                        $end = $end instanceof DateTimeInterface ?
                            DateTimeService::createStringFromDateTime(
                                dateTime: $end,
                                format: CommonDef::API_DATE_FORMAT
                            ) : $end;
                        if (null === $end) {
                            break;
                        }
                        $end .= ' 23:59:59';
                        $filterNameEnd = $filterName . 'End';
                        $qb->setParameter(key: $filterNameEnd, value: $end);
                        $where->add("{$columnName} <= :{$filterNameEnd}");
                    }
                    if (isset($filterValue['start']) || isset($filterValue['end'])) {
                        $defaultParameter = false;
                        $andWhere = $where;
                    }
                    break;
                case self::FILTER_RANGE:
                    $defaultParameter = false;
                    $where = new Expr\Andx();
                    if (is_array($filterValue)) {
                        $from = $filterValue['start'] ?? null;
                        if ($from) {
                            $from = is_string($from) ? intval($from) : $from;
                            $where->add(
                                $qb->andWhere()->expr()->gte(
                                    $columnName,
                                    ':' . $filterName . 'Start'
                                )
                            );
                            $qb->setParameter($filterName . 'Start', $from, Types::INTEGER);
                        }
                        $to = $filterValue['end'] ?? null;
                        if ($to) {
                            $to = is_string($to) ? intval($to) : $to;
                            $where->add(
                                $qb->andWhere()->expr()->lte(
                                    $columnName,
                                    ':' . $filterName . 'End'
                                )
                            );
                            $qb->setParameter($filterName . 'End', $to, Types::INTEGER);
                        }
                        $andWhere = $where;
                    }
                    break;
                case self::FILTER_LIST:
                    $defaultParameter = false;
                    $andWhere = $qb->expr()->andX(
                        $qb->expr()->in($columnName, $filterValue)
                    );
                    break;
                case self::FILTER_UUID:
                    $paramType = Types::STRING;
                    break;
                default:
                    break;
            }

            if (isset($andWhere)) {
                $qb->andWhere($andWhere);
                if ($defaultParameter) {
                    $qb->setParameter($filterName, $filterValue, $paramType);
                }
            }
        }
    }
}
