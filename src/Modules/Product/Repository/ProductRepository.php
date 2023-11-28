<?php

declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Entity\DbDef;
use App\Entity\References\Storage;
use App\Model\DataList;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Model\ProductList;
use App\Repository\Common\Interfaces as CmnIntf;
use App\Repository\RepositoryDef;
use App\Repository\Traits as CommonTraits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 */
class ProductRepository extends ServiceEntityRepository implements CmnIntf\RepositoryInterface
{
    use CommonTraits\BaseRepositoryTrait;
    use CommonTraits\NameRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @var array<string, array{name: string, type: string}> FILTER
     */
    public const FILTER = [
        'id' => ['name' => 'self.id', 'type' => DbDef::TBL_COL_ID_TYPE],
        'name' => ['name' => 'self.name', 'type' => Types::STRING],
        'price' => ['name' => 'self.price', 'type' => Types::DECIMAL],
        'quantityAvailableForOrder' => ['name' => 'self.quantityAvailableForOrder', 'type' => Types::INTEGER],
        'storage' => ['name' => 's.id', 'type' => RepositoryDef::FILTER_LIST],
    ];

    /**
     * @var array<string, string> SORT
     */
    public const SORT = [
        'id' => 'self.id',
        'name' => 'self.name',
        'price' => 'self.price',
        'quantityAvailableForOrder' => 'self.quantityAvailableForOrder',
        'storage' => 's.name',
    ];


    public function list(null|ProductList $list = null): DataList
    {
        $qb = $this->createQueryBuilder('self');

        $qb
            ->innerJoin(Storage::class, 's', Join::WITH, $qb->expr()->andx(
                $qb->expr()->eq('self.storage', 's'),
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
