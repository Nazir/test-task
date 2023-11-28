<?php

namespace App\Repository\References\Interfaces;

/**
 * @method object|null find($id, $lockMode = null, $lockVersion = null)
 * @method object|null findOneBy(array $criteria, array $orderBy = null)
 * @method object[]    findAll()
 * @method object[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface BaseReferenceRepositoryInterface
{
    public function findByName(string|null $name, bool $throw = false): object|null;
}
