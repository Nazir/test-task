<?php

namespace App\Model\Filters\Traits;

use Symfony\Component\Validator\Constraints as Assert;

trait ModeTrait
{
    #[Assert\Choice(['default', 'all', 'onlyTotal'])]
    public string $mode = 'default';
}
