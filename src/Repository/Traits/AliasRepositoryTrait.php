<?php

namespace App\Repository\Traits;

use App\Entity\EntityDef;
use App\Exception\ValidationException;
use App\Utils\StringUtils;

trait AliasRepositoryTrait
{
    public function findByAlias(string|null $alias, bool $prepare = true, bool $throw = false): object|null
    {
        $return = null;

        if (isset($alias)) {
            $return = $this->findOneBy([EntityDef::COL_ALIAS => $prepare ? StringUtils::slug($alias) : $alias]);

            if ($throw && null === $return) {
                throw new ValidationException("$alias not found", code: 0);
            }
        }

        return $return;
    }
}
