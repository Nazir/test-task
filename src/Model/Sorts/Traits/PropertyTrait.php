<?php

namespace App\Model\Sorts\Traits;

trait PropertyTrait
{
    public function setProperty(string $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
