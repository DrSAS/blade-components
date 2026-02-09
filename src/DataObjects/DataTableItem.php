<?php

namespace DrSAS\BladeComponents\DataObjects;

readonly class DataTableItem
{
    public function __construct(
        public array $visible,
        public array $hidden = [],
    ) {}

    public static function make(
        array $visible,
        array $hidden = []
    ): self {
        return new self($visible, $hidden);
    }
}