Memcache Search
==============
PHP library that allows to search keys in memcache. It uses `lru_crawler` non-blocking mechanism to iterate between memcache keys. 
 This libary *does not require memcache extension to be installed*.

Installation
------------
```bash
composer require qmegas/memcache-search
```

Requirements
------------
PHP >= 7.0
Memcache server should work on unix-like system

Usage Examples
--------------
```php
<?php

$search = new \Qmegas\MemcacheSearch();
$search->addServer('127.0.0.1', 11211);

//Inline search in key name
$find = new \Qmegas\Finder\Inline('test');
foreach ($search->search($find) as $item) {
	echo "Key: {$item->getKey()} expires ".($item->getExpiration() === -1 ? 'NEVER' : 'on '.date('d/m/Y H:m:i', $item->getExpiration()))."\n";
}

//Inline search in key name - method 2
foreach ($search->search('test') as $item) {
	...
}

//Searching for non expiring items
$find = new \Qmegas\Finder\ExpiresNever();
foreach ($search->search($find) as $item) {
	...
}

//Searching in name by using regular expression
$find = new \Qmegas\Finder\RegExp('/Test([0-9]*)/i');
foreach ($search->search($find) as $item) {
	...
}

//Custom search logic
foreach ($search->search(function(\Qmegas\MemcacheItem $item): bool {
	//Your logic is here
}) as $item) {
	...
}
```