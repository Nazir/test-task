<?php

declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Order\Entity\OrderStatus;
use App\Repository\References\Interfaces\BaseSystemReferenceRepositoryInterface;
use App\Repository\References\Traits\BaseSystemReferenceRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderStatus[]    findAll()
 * @method OrderStatus[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 */
class OrderStatusRepository extends ServiceEntityRepository implements BaseSystemReferenceRepositoryInterface
{
    use BaseSystemReferenceRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderStatus::class);
    }
}
