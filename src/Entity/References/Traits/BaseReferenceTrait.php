<?php

namespace App\Entity\References\Traits;

use App\Entity\Traits as ET;

trait BaseReferenceTrait
{
    use ET\IdTrait;
    use ET\NameTrait;
    use ET\DeletedTrait;

    public function __toString(): string
    {
        return $this->name;
    }
}
