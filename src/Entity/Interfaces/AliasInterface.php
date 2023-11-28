<?php

namespace App\Entity\Interfaces;

interface AliasInterface
{
    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Set alias
     *
     * @param string $name
     *
     * @return self
     */
    public function setAlias(string $alias): self;
}
