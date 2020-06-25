Memcache Search
==============
PHP library that allows to search keys in memcache. It uses `lru_crawler` non-blocking mechanism to iterate between memcache keys. 
 This libary **does not require memcache extension to be installed**.
 
Why it's better then other solution?
------------------------------------
1. Standart [`Memcached`](https://www.php.net/manual/en/book.memcached.php) extension have function `getAllKeys` which is based on [`memcached_dump`](http://docs.libmemcached.org/memcached_dump.html) function which is not guarentee to dump all keys. Also it [reported](https://www.php.net/manual/en/memcached.getallkeys.php#123793) that staring memcache 1.4.23 this function does not work.
2. Standart [`Memcache`](https://www.php.net/manual/en/book.memcache.php) extension does not have such functionallity and have [different](https://stackoverflow.com/questions/9831395/how-can-i-query-memcached-with-php-to-get-a-list-of-all-its-keys-in-storage) [solutions](https://stackoverflow.com/questions/19560150/get-all-keys-set-in-memcached) based on `stats cachedump` which have memcache server performance impact and once again not guarentee to dump all keys.

Installation
------------
```bash
composer require qmegas/memcache-search
```

Requirements
------------
* PHP >= 7.0
* Memcache server should work on unix-like system and be >= 1.4.24

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

License
-------

The library is open-sourced software licensed under the [MIT license](LICENSE.md).