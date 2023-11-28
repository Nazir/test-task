<?php

namespace App\Model\Sorts\Traits;

use App\Repository\RepositoryDef;
use Symfony\Component\Validator\Constraints as Assert;

trait SortTrait
{
    use PropertyTrait;

    #[Assert\NotBlank()]
    #[Assert\Choice([RepositoryDef::CRITERIA_ASC, RepositoryDef::CRITERIA_DESC, 'asc', 'desc'])]
    private string $order = RepositoryDef::CRITERIA_ASC;

    public function setOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getOrder(): string
    {
        return $this->order;
    }
}
