<?php

namespace App\Repository\References\Interfaces;

/**
 * @method object|null find($id, $lockMode = null, $lockVersion = null)
 * @method object|null findOneBy(array $criteria, array $orderBy = null)
 * @method object[]    findAll()
 * @method object[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface BaseSystemReferenceRepositoryInterface
{
    public function findByName(string|null $name, bool $throw = false): object|null;
    public function findByAlias(string|null $alias, bool $prepare = true, bool $throw = false): object|null;
}
