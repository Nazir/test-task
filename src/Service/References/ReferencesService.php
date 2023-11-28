<?php

declare(strict_types=1);

namespace App\Service\References;

use App\Entity\EntityDef;
use App\Entity\References\Interfaces as RI;
use App\Exception as Except;
use App\Service\BaseService;
use App\Service\Common\SerializerService;
use App\Service\Common\EntityService;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

use function in_array;
use function key_exists;

final class ReferencesService extends BaseService
{
    /**
     * @var array<string, string> SORT
     */
    public const SORT = [
        EntityDef::COL_ID => EntityDef::COL_ID,
        EntityDef::COL_NAME => EntityDef::COL_NAME,
    ];

    /**
     * @var array<string, string[]> EXCLUDE_COLUMNS
     */
    public const EXCLUDE_COLUMNS = [];

    public function __construct(
        private readonly EntityService $entityService,
        private readonly TranslatorInterface $translator,
        private readonly SerializerService $serializerService,
    ) {
    }

    /**
     * List all
     *
     * @return null|array List all
     */
    public function listAll(): null|array
    {
        $data = [];
        foreach (array_keys(ReferencesDef::MAP) as $refName) {
            $list = new Model\ReferencesList();

            if (true === key_exists($refName, self::EXCLUDE_COLUMNS)) {
                $excludeColumns = self::EXCLUDE_COLUMNS[$refName] ?? [];
                if (false === in_array(EntityDef::COL_DELETED, $excludeColumns)) {
                    // $list->filter = [EntityDef::COL_DELETED => false];
                }
            } else {
                // $list->filter = [EntityDef::COL_DELETED => false];
            }

            $data[$refName] = $this->list(refName: $refName, list: $list);
        }

        return $data;
    }

    /**
     * List
     *
     * @return null|object[]|array List of reference objects
     *
     * @throws Exception
     */
    public function list(string $refName, null|Model\ReferencesList $list = null): null|array
    {
        $repository = $this->entityService->getRepository(ReferencesDef::MAP[$refName]);

        if (null === $list) {
            return $repository->findAll();
        }

        $criteria = [];
        if (!empty($list->filter)) {
            $criteria = $list->filter;
        }

        $orderBy = null;
        if (!empty($list->sort)) {
            /** @var array<string, string> $orderBy */
            $orderBy[self::SORT[$list->sort->getProperty()]] = $list->sort->getOrder();
        }

        return $repository->findBy(criteria: $criteria, orderBy: $orderBy);
    }

    /**
     * Create
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function create(string $refName, mixed $data, bool $throw = true): RI\ReferenceInterface
    {
        /** @var RI\ReferenceInterface $obj */
        $obj = $this->serializerService->deserialize(obj: null, data: $data, type: ReferencesDef::MAP[$refName]);

        $tryFind = $this->findByName(refName: $refName, name: $obj->getName(), throw: false);
        if (true === $throw && isset($tryFind)) {
            throw new Except\ValidationException(
                message: [
                    'name' => $this->translator->trans(
                        'record.name.already.exists',
                        ["%name%" => $obj->getName()],
                    )
                ],
                code: ExceptionsDef::EXCEPTION_CODE_REF_CREATE,
            );
        }

        $this->entityService->save($obj);

        return $obj;
    }

    /**
     * Read
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function read(
        string $refName,
        null|int|RI\ReferenceInterface|RI\SystemReferenceInterface $objOrObjId,
        bool $throw = true,
    ): null|RI\ReferenceInterface|RI\SystemReferenceInterface {
        if (is_int($objOrObjId)) {
            $refClass = ReferencesDef::MAP[$refName];
            $repository = $this->entityService->getRepository($refClass);

            $obj = $repository->find($objOrObjId);

            if (true === $throw && null === $obj) {
                throw $this->entityService->createObjectNotFountException(
                    objOrClass: $refClass,
                    id: $objOrObjId,
                    propertyName: $refName,
                    code: ExceptionsDef::EXCEPTION_CODE_REF_READ,
                );
            }
        } else {
            $obj = $objOrObjId;
        }

        return $obj instanceof RI\ReferenceInterface
            || $obj instanceof RI\SystemReferenceInterface ? $obj : null;
    }

    /**
     * Update
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function update(
        string $refName,
        int $objOrObjId,
        mixed $data,
        bool $throw = true,
    ): null|RI\ReferenceInterface|RI\SystemReferenceInterface {
        $obj = $this->read(refName: $refName, objOrObjId: $objOrObjId, throw: $throw);

        if (null === $obj) {
            $refClass = ReferencesDef::MAP[$refName];
            throw $this->entityService->createObjectNotFountException(
                objOrClass: $refClass,
                id: $objOrObjId,
                propertyName: $refName,
                code: ExceptionsDef::EXCEPTION_CODE_REF_READ,
            );
        }

        $this->serializerService->deserialize(obj: $obj, data: $data, type: $obj::class);

        $tryFind = false;
        $name = '-';

        if ($obj instanceof RI\ReferenceInterface) {
            $tryFind = $this->findByName(refName: $refName, name: $obj->getName(), throw: false);
        } elseif ($obj instanceof RI\SystemReferenceInterface) {
            $tryFind = $this->findByAlias(refName: $refName, alias: $obj->getAlias(), throw: false);
        }

        if (true === $throw && isset($tryFind)) {
            throw new Except\ValidationException(
                message: [
                    'name' => $this->translator->trans(
                        'record.name.already.exists',
                        ["%name%" => $name],
                    )
                    ],
                code: ExceptionsDef::EXCEPTION_CODE_REF_CREATE,
            );
        }

        $this->entityService->save($obj);

        return $obj;
    }

    /**
     * Delete
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function delete(
        string $refName,
        int|RI\ReferenceInterface|RI\SystemReferenceInterface $objOrObjId,
        bool $throw = true,
    ): void {
        $obj = $this->read(refName: $refName, objOrObjId: $objOrObjId, throw: $throw);

        if ($obj instanceof RI\ReferenceInterface) {
            // $this->entityService->remove($obj);
            $obj->setDeleted();
            $this->entityService->save($obj);
        } elseif ($obj instanceof RI\SystemReferenceInterface) {
            // TODO: Check rights & soft delete?
        }
    }

    /**
     * Restore
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function restore(
        string $refName,
        int|RI\ReferenceInterface|RI\SystemReferenceInterface $objOrObjId,
        bool $throw = true,
    ): void {
        $obj = $this->read(refName: $refName, objOrObjId: $objOrObjId, throw: $throw);

        if ($obj instanceof RI\ReferenceInterface) {
            $obj->setDeleted(restore: true);
            $this->entityService->save($obj);
        } elseif ($obj instanceof RI\SystemReferenceInterface) {
            // TODO: Check rights & soft delete?
        }
    }

    /**
     * Find by id
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function find(
        string $refName,
        null|int $id,
        bool $throw = true,
    ): null|RI\ReferenceInterface|RI\SystemReferenceInterface {
        if (null === $id) {
            return null;
        }

        $repository = $this->entityService->getRepository(ReferencesDef::MAP[$refName]);

        $obj = $repository->find($id);

        if (true === $throw && null === $obj) {
            $refClass = ReferencesDef::MAP[$refName];
            throw $this->entityService->createObjectNotFountException(
                objOrClass: $refClass,
                id: $id,
                propertyName: $refName,
                code: ExceptionsDef::EXCEPTION_CODE_REF_READ,
            );
        }

        return $obj instanceof RI\ReferenceInterface
            || $obj instanceof RI\SystemReferenceInterface ? $obj : null;
    }

    /**
     * Find by name
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function findByName(
        string $refName,
        null|string $name,
        bool $throw = true,
    ): null|RI\ReferenceInterface|RI\SystemReferenceInterface {
        if (null === $name) {
            return null;
        }

        $repository = $this->entityService->getRepository(ReferencesDef::MAP[$refName]);

        $obj = $repository->findOneBy(['name' => $name]);

        if (true === $throw && null === $obj) {
            $refClass = ReferencesDef::MAP[$refName];
            throw $this->entityService->createObjectNotFountException(
                objOrClass: $refClass,
                id: '',
                propertyName: $refName,
                code: ExceptionsDef::EXCEPTION_CODE_REF_READ,
            );
        }

        return $obj instanceof RI\ReferenceInterface
            || $obj instanceof RI\SystemReferenceInterface ? $obj : null;
    }

    /**
     * Find by alias
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function findByAlias(
        string $refName,
        null|string $alias,
        bool $throw = true,
    ): null|RI\SystemReferenceInterface {
        if (null === $alias) {
            return null;
        }

        $repository = $this->entityService->getRepository(ReferencesDef::MAP[$refName]);

        $obj = $repository->findOneBy(['alias' => $alias]);

        if (true === $throw && null === $obj) {
            $refClass = ReferencesDef::MAP[$refName];
            throw $this->entityService->createObjectNotFountException(
                objOrClass: $refClass,
                id: '',
                propertyName: $refName,
                code: ExceptionsDef::EXCEPTION_CODE_REF_READ,
            );
        }

        return $obj instanceof RI\SystemReferenceInterface ? $obj : null;
    }
}
