<?php
class CacheUtil {
	private static $instance = null;
	private $memcache = null;

	public static function getInstance() {
		if (self::$instance==null) {
			self::$instance = new CacheUtil();
		}

		return self::$instance->getCacheObj();
	}

	private function __construct() {
		global $cache_servers;
		$this->memcache = new Memcached();

		foreach($cache_servers as $host=>$port) {
			$this->memcache->addServer($host, $port);
		}
	}

	private function getCacheObj() {
		return $this->memcache;
	}
}
?>