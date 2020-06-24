<?php

namespace Qmegas;

class MemcacheItem
{
	private $key = '';
	private $expires = 0;
	private $server = '';
	private $rawData = '';
	
	public function __construct(string $data)
	{
		$this->rawData = $data;
		$info = $this->parse($data);
		$this->key = urldecode($info['key']);
		$this->expires = (int)$info['exp'];
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
	
	public function getServer(): string
	{
		return $this->server;
	}
	
	public function getRawData(): string
	{
		return $this->rawData;
	}
	
	/**
	 * @param string|callable $pattern
	 * @return bool
	 */
	public function match($pattern): bool
	{
		if (is_string($pattern)) {
			$ret = strpos($this->key, $pattern) !== false;
		} else if (is_callable($pattern)) {
			$ret = $pattern($this);
		}
		return $ret;
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