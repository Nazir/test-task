<?php

declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Entity\DbDef;
use App\Model\DataList;
use App\Modules\Customer\Entity\Customer;
use App\Modules\Order\Entity\Order;
use App\Modules\Order\Entity\OrderStatus;
use App\Modules\Order\Model\OrderList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\Common\Interfaces as CmnIntf;
use App\Repository\RepositoryDef;
use App\Repository\Traits as CommonTraits;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 */
class OrderRepository extends ServiceEntityRepository implements CmnIntf\RepositoryInterface
{
    use CommonTraits\BaseRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @var array<string, array{name: string, type: string}> FILTER
     */
    public const FILTER = [
        'id' => ['name' => 'self.id', 'type' => DbDef::TBL_COL_ID_TYPE],
        'date' => ['name' => 'self.date', 'type' => RepositoryDef::FILTER_PERIOD],
    ];

    /**
     * @var array<string, string> SORT
     */
    public const SORT = [
        'id' => 'self.id',
        'date' => 'self.date',
    ];

    public function list(null|OrderList $list = null): DataList
    {
        $qb = $this->createQueryBuilder('self');

        $qb
            ->innerJoin(Customer::class, 'c', Join::WITH, $qb->expr()->andx(
                $qb->expr()->eq('self.customer', 'c'),
            ))
            ->innerJoin(OrderStatus::class, 's', Join::WITH, $qb->expr()->andx(
                $qb->expr()->eq('self.status', 's'),
            ));

        $onlyTotal = false;
        switch ($list?->mode) {
            case 'all':
                break;
            case 'onlyTotal':
                $onlyTotal = true;
                break;
            case 'default':
            default:
                break;
        }

        RepositoryDef::addFilters(qb: $qb, filters: $list?->filter, filtersMap: self::FILTER);

        if (false === $onlyTotal) {
            if (isset($list->sort)) {
                $qb->orderBy(self::SORT[$list->sort->getProperty()], $list->sort->getOrder());
            }
        }

        $paginator = new Paginator($qb, false);

        $dataList = new DataList($list?->page);
        $dataList->setTotal($paginator->count());

        if (false === $onlyTotal) {
            $paginator
                ->getQuery()
                ->setFirstResult($dataList->offset())
                ->setMaxResults($dataList->limit());

            $dataList->setData($paginator->getIterator());
        }

        return $dataList;
    }
}
