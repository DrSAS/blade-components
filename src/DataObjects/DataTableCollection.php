<?php

namespace DrSAS\BladeComponents\DataObjects;

use Illuminate\Support\Collection;

class DataTableCollection extends Collection
{
    public readonly string $idKey;

    public function __construct(iterable $items = [], string $idKey = 'id')
    {
        parent::__construct($items);
        $this->idKey = $idKey;
    }

    public static function make($items = [], string $idKey = 'id'): self
    {
        return new self($items, $idKey);
    }

    public function addRow(array $visible, array $hidden = []): self
    {
        $this->push(new DataTableItem($visible, $hidden));
        return $this;
    }

    public function columns(): array
    {
        return $this->isNotEmpty()
            ? array_keys($this->first()->visible)
            : [];
    }

    public function getId(DataTableItem $item): mixed
    {
        return $item->visible[$this->idKey] ?? $item->hidden[$this->idKey] ?? null;
    }

    public function getVisibleValues(): array
    {
        return $this->map(fn (DataTableItem $row) =>
            array_map(fn ($value) => $value ?? '', array_values($row->visible))
        )->toArray();
    }

    public function getIds(): array
    {
        return $this->map(fn (DataTableItem $row) => $this->getId($row))->toArray();
    }
}