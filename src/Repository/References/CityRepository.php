<?php

declare(strict_types=1);

namespace App\Repository\References;

use App\Entity\References\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 */
class CityRepository extends ServiceEntityRepository implements Interfaces\ReferencesRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }
}
