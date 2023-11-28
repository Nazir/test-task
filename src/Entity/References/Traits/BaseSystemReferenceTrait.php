<?php

namespace App\Entity\References\Traits;

use App\Entity\Traits as ET;

trait BaseSystemReferenceTrait
{
    use ET\IdTrait;
    use ET\NameTrait;
    use ET\AliasTrait;
    use ET\DeletedTrait;

    public function __toString(): string
    {
        return $this->alias;
    }
}
