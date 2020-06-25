<?php

namespace Qmegas\Finder;

class ExpiresNever implements FinderInterface 
{
    public function match(\Qmegas\MemcacheItem $item): bool 
    {
        return ($item->getExpiration() === -1);
    }
}
