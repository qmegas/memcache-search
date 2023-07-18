<?php

namespace Qmegas;

class MemcacheSearch 
{
    public const ON_BUSY_IGNORE = 0;
    public const ON_BUSY_EXCEPTION = 1;
    public const ON_BUSY_TIMEOUT = 2;
    
    private $servers = [];
    private $busyStrategy = self::ON_BUSY_EXCEPTION;
    private $busyTimeout = 5;

    public function addServer(string $domain, int $port = 11211) 
    {
        $this->servers[] = [$domain, $port];
    }
    
    public function onBusyStrategy(int $strategy, int $timeout = 5)
    {
        $this->busyStrategy = $strategy;
        
        if ($strategy === self::ON_BUSY_TIMEOUT) {
            $this->busyTimeout = $timeout;
        }
    }

    /**
     * @param string|callable|Finder\FinderInterface $finder
     * @return MemcacheItem[]
     * @throws Exception\ConnectionException
     */
    public function search($finder) 
    {
        if (is_string($finder)) {
            $finder = new Finder\Inline($finder);
        }
        
        foreach ($this->servers as list($domain, $port)) {
            $serverBusy = false;
            $fp = fsockopen($domain, $port);
            if (!$fp) {
                throw new Exception\ConnectionException("Can not connect to {$domain}:{$port}");
            }

            fputs($fp, "lru_crawler metadump all\n");

            $part = '';
            while (true) {
                $part .= fgets($fp, 1024);
                $lines = $this->extractLines($part);
                
                foreach ($lines as $line) {
                    $line = trim($line);

                    if ($line === 'END' || $line === 'ERROR' || $line === '') {
                        break 2;
                    } elseif (substr($line, 0, 5) === 'BUSY ') {
                        $excMessage = "Crawler busy for server {$domain}:{$port}";
                        if ($this->busyStrategy === self::ON_BUSY_IGNORE) {
                            break 2;
                        } elseif ($this->busyStrategy === self::ON_BUSY_EXCEPTION) {
                            fclose($fp);
                            throw new Exception\BusyException($excMessage);
                        } elseif ($this->busyStrategy === self::ON_BUSY_TIMEOUT) {
                            if ($serverBusy) {
                                fclose($fp);
                                throw new Exception\BusyException("{$excMessage} after waiting {$this->busyTimeout} seconds");
                            }
                            
                            $serverBusy = true;
                            sleep($this->busyTimeout);
                            continue;
                        }
                    }
                    
                    $serverBusy = false;

                    $key = new MemcacheItem($line);
                    $match = is_callable($finder) ? $finder($key) : $finder->match($key);
                    if (!$match) {
                        continue;
                    }
                    $key->setServer($domain);

                    yield $key;
                }
            }

            fclose($fp);
        }

        return;
    }

    private function extractLines(string &$data): array 
    {
        $lines = explode("\n", $data);
        $data = array_pop($lines);
        return $lines;
    }
}
