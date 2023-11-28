<?php

declare(strict_types=1);

namespace App\Repository\References;

use App\Entity\References\Storage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Storage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Storage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Storage[]    findAll()
 * @method Storage[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 */
class StorageRepository extends ServiceEntityRepository implements Interfaces\ReferencesRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Storage::class);
    }
}
