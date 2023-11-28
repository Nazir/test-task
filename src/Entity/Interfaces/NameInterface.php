<?php

namespace App\Entity\Interfaces;

interface NameInterface
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self;
}
