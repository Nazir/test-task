<?php

declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Order\OrderProduct;
use App\Repository\Common\Interfaces as CmnIntf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProduct[]    findAll()
 * @method OrderProduct[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 */
class OrderProductRepository extends ServiceEntityRepository implements CmnIntf\RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }
}
