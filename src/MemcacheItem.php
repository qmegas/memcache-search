<?php

namespace Qmegas;

class MemcacheItem 
{
    private $key = '';
    private $expires = 0;
    private $lastAccessTime = 0;
    private $dataSize = 0;
    private $server = '';
    private $rawData = '';

    public function __construct(string $data) 
    {
        $this->rawData = $data;
        $info = $this->parse($data);
        $this->key = urldecode($info['key']);
        $this->expires = (int)$info['exp'];
        $this->lastAccessTime = (int)$info['la'];
        $this->dataSize = (int)$info['size'];
    }

    public function setServer(string $server) 
    {
        $this->server = $server;
    }

    public function getKey(): string 
    {
        return $this->key;
    }

    public function getExpiration(): int 
    {
        return $this->expires;
    }
    
    public function getLastAccessTime(): int 
    {
        return $this->lastAccessTime;
    }
    
    public function getDataSize(): int 
    {
        return $this->dataSize;
    }

    public function getServer(): string 
    {
        return $this->server;
    }

    public function getRawData(): string 
    {
        return $this->rawData;
    }

    private function parse(string $data): array 
    {
        $info = [];

        foreach (explode(' ', $data) as $part) {
            list($key, $val) = explode('=', $part);
            $info[$key] = $val;
        }

        return $info;
    }
}
