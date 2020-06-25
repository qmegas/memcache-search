<?php

namespace Qmegas\Finder;

interface FinderInterface 
{
    public function match(\Qmegas\MemcacheItem $item): bool;
}
