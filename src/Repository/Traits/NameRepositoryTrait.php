<?php

namespace App\Repository\Traits;

use App\Exception\ValidationException;

trait NameRepositoryTrait
{
    public function findByName(string|null $name, bool $throw = false): object|null
    {
        $return = null;

        if (isset($name)) {
            $return = $this->findOneBy(['name' => $name]);
            if ($throw && null === $return) {
                throw new ValidationException("$name not found", code: 0);
            }
        }

        return $return;
    }
}
