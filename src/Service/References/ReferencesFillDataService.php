<?php

namespace App\Service\References;

use App\Entity\References\Interfaces\ReferenceInterface;
use App\Entity\References\Interfaces\SystemReferenceInterface;
use App\Repository\References\Interfaces\BaseReferenceRepositoryInterface;
use App\Repository\References\Interfaces\BaseSystemReferenceRepositoryInterface;
use App\Service\Common\EntityService;

use function is_a;

class ReferencesFillDataService
{
    public function __construct(
        private readonly EntityService $entityService,
    ) {
    }

    /**
     * Fill data
     *
     * @param bool  $truncate Truncate table(s)
     * @param array $entities Entities
     * @param array $list     List of allowed entities
     *
     * @return bool
     */
    public function fill(
        bool $truncate = false,
        array $entities = [],
        array $list = ReferencesFillDataDef::INTRASYSTEM_ENTITY,
    ): bool {
        /** @var array<string, array<string, string>> $entity */
        foreach ($list as $entityName => $entity) {
            if (!empty($entities) && !in_array($entityName, $entities)) {
                continue;
            }

            dump($entityName);

            /** @var class-string $entityClass */
            $entityClass = $entity['class'];
            if ($truncate) {
                $this->entityService->truncateTable(entityClass: $entityClass, cascade: true);
            }

            foreach ($entity['data'] as $alias => $name) {
                if (
                    is_a($entityClass, ReferenceInterface::class, true)
                    || is_a($entityClass, SystemReferenceInterface::class, true)
                ) {
                    $repository = $this->entityService->getRepository($entityClass);
                    if (is_a($entityClass, SystemReferenceInterface::class, true)) {
                        /** @var BaseSystemReferenceRepositoryInterface $repository */
                        /** @var SystemReferenceInterface $obj */
                        $obj = $repository->findByAlias(alias: $alias);
                        if ($obj instanceof SystemReferenceInterface) {
                            $obj->setName($name);
                        } else {
                            $obj = new $entityClass(name: $name, alias: $alias);
                        }
                    } else {
                        /** @var BaseReferenceRepositoryInterface $repository */
                        /** @var ReferenceInterface $obj */
                        $obj = $repository->findByName(name: $name);
                        if ($obj instanceof SystemReferenceInterface) {
                        } else {
                            $obj = new $entityClass(name: $name);
                        }
                    }

                    $this->entityService->save($obj);
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}
