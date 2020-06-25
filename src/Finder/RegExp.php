<?php

namespace Qmegas\Finder;

class RegExp implements FinderInterface 
{
    /** @var string */
    private $pattern;

    public function __construct(string $pattern) 
    {
        $this->pattern = $pattern;
    }

    public function match(\Qmegas\MemcacheItem $item): bool 
    {
        return (preg_match($this->pattern, $item->getKey()) === 1);
    }
}
