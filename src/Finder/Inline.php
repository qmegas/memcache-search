<?php

namespace Qmegas\Finder;

class Inline implements FinderInterface 
{
    /** @var string */
    private $searchString;

    public function __construct(string $searchString) 
    {
        $this->searchString = $searchString;
    }

    public function match(\Qmegas\MemcacheItem $item): bool 
    {
        return strpos($item->getKey(), $this->searchString) !== false;
    }
}
