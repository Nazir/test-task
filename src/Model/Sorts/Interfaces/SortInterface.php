<?php

namespace App\Model\Sorts\Interfaces;

interface SortInterface
{
    public function getProperty(): string;

    public function setProperty(string $property): self;

    public function getOrder(): string;

    public function setOrder(string $order): self;
}
