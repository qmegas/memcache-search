<?php

namespace Qmegas;

class MemcacheSearch
{
	private $servers = [];
	
	public function addServer(string $domain, int $port = 11211)
	{
		$this->servers[] = [$domain, $port];
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
					}
					
					$key = new MemcacheItem($line);
					if (!$finder->match($key)) {
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