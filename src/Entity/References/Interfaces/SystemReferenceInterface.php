<?php

namespace App\Entity\References\Interfaces;

use App\Entity\Interfaces as EI;

interface SystemReferenceInterface extends BaseReferenceInterface, EI\NameInterface, EI\AliasInterface
{
}
