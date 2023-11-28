<?php

namespace App\Entity\Interfaces;

use DateTimeInterface;

interface BaseInterface extends GetIdInterface
{
    /**
     * Get deleted
     */
    public function getDeleted(): null|DateTimeInterface;

    /**
     * Set deleted
     */
    public function setDeleted(null|DateTimeInterface $deleted = null, bool $restore = false): self;
}
