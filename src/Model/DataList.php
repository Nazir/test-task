<?php

namespace App\Model;

use ArrayIterator;

use function array_merge;
use function count;

class DataList
{
    private Page $page;

    private ArrayIterator|iterable $data = [];

    private int $total = 0;

    public function __construct(null|Page $page)
    {
        $this->page = $page ?? new Page();
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function setPage(Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getData(): ArrayIterator|iterable
    {
        return $this->data;
    }

    public function setData(ArrayIterator|iterable $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function count(): int
    {
        return count((array) $this->data);
    }

    public function limit(): int
    {
        return $this->getPage()->getSize();
    }

    public function offset(): int
    {
        return $this->getPage()->getSize() * ($this->getPage()->getNumber() - 1);
    }

    public function paginator(): array
    {
        $first = $this->page->getSize() * ($this->page->getNumber() - 1);
        $last = $first + ($this->page->getSize() - 1);
        $total = $this->getTotal();
        if ($total === 0) {
            $first = 0;
            $last = 0;
        } elseif ($last > $total - 1) {
            $last = $total - 1;
            if ($first > $last) {
                $first = $last;
            }
        }

        return [
            'paginator' => [
                'first' => $first,
                'last' => $last,
                'total' => $total,
                'pageNumber' => $this->page->getNumber(),
                'pageSize' => $this->page->getSize(),
            ]
        ];
    }

    public function list(array|null $data = null): array
    {
        $data = $data ?? $this->getData();

        return array_merge($this->paginator(), ['data' => $data]);
    }

    /**
     * Data
     *
     * @return ArrayIterator|iterable Data
     */
    public function __invoke(): iterable
    {
        return $this->list();
    }
}
