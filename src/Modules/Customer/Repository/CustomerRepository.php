<?php

declare(strict_types=1);

namespace App\Modules\Customer\Repository;

use App\Entity\DbDef;
use App\Entity\References\City;
use App\Model\DataList;
use App\Modules\Customer\Entity\Customer;
use App\Modules\Customer\Model\CustomerList;
use App\Repository\Common\Interfaces as CmnIntf;
use App\Repository\RepositoryDef;
use App\Repository\Traits as CommonTraits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository implements CmnIntf\RepositoryInterface
{
    use CommonTraits\BaseRepositoryTrait;
    use CommonTraits\NameRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * @var array<string, array{name: string, type: string}> FILTER
     */
    public const FILTER = [
        'id' => ['name' => 'self.id', 'type' => DbDef::TBL_COL_ID_TYPE],
        'name' => ['name' => 'self.name', 'type' => Types::STRING],
        'phone' => ['name' => 'self.phone', 'type' => Types::STRING],
        'email' => ['name' => 'self.email', 'type' => Types::STRING],
        'deliveryAddressCity' => ['name' => 'c.id', 'type' => RepositoryDef::FILTER_LIST],
    ];

    /**
     * @var array<string, string> SORT
     */
    public const SORT = [
        'id' => 'self.id',
        'name' => 'self.name',
        'phone' => 'self.phone',
        'email' => 'self.email',
        'deliveryAddressCity' => 'c.name',
        'deliveryAddressStreet' => 'self.deliveryAddress.street',
        'deliveryAddressHouseNumber' => 'self.deliveryAddress.houseNumber',
        'deliveryAddressApartmentNumber' => 'self.deliveryAddress.apartmentNumber',
    ];


    public function list(null|CustomerList $list = null): DataList
    {
        $qb = $this->createQueryBuilder('self');

        $qb
            ->innerJoin(City::class, 'c', Join::WITH, $qb->expr()->andx(
                $qb->expr()->eq('self.city', 'c'),
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
