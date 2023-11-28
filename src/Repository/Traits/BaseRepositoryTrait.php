<?php

namespace App\Repository\Traits;

trait BaseRepositoryTrait
{
    /**
     * Gets the fully qualified name of the table.
     *
     * @param class-string $className Name of class
     */
    private function getFullTableName(string $className): string
    {
        $entityManager = $this->getEntityManager();
        $table = $entityManager->getClassMetadata($className)->table;

        return "{$table['schema']}.{$table['name']}";
    }
}
