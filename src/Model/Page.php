<?php

namespace App\Model;

use App\Common\CommonDef;
use Symfony\Component\Validator\Constraints as Assert;

class Page
{
    #[Assert\Positive()]
    #[Assert\Type('integer')]
    private int $number = 1;

    #[Assert\Positive()]
    #[Assert\Type('integer')]
    #[Assert\LessThanOrEqual(CommonDef::DATA_LIST_LIMIT_MAX)]
    private int $size = CommonDef::DATA_LIST_LIMIT;

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }
}
