<?php
class DSession {

	public static $SESSION_KEY = 'CONFONESESSIONID';
    private static $AUTHINDEX = 'auth_index';

	private $sessionId = null;
	private $sessionCache = null;

	private static $DSESSION = null;


	public static function instance() {
		if (!isset(self::$DSESSION)) {
			self::$DSESSION = new DSession();
		}

		return self::$DSESSION;
	}

	private function __construct() {
		$this->sessionCache = CacheUtil::getInstance();

		if (isset($_COOKIE[self::$SESSION_KEY])) {
			$this->sessionId = $_COOKIE[self::$SESSION_KEY];
		} else {
			global $component_name;

			$time = md5(microtime());
			$rand = md5(rand(0, 10000));
			$this->sessionId = $component_name.substr($rand, 0, 5).substr($time, -10, 10);

			while ($this->sessionCache->get($this->sessionId)) {
				usleep(rand(100, 1000));

				$time = md5(microtime());
				$rand = md5(rand(0, 10000));
				$this->sessionId = $component_name.substr($rand, 0, 5).substr($time, -10, 10);
			}

			setcookie(self::$SESSION_KEY, $this->sessionId, 0, '/', 'confone.com', false, true);
		}
	}

	public function set($key, $value) {
		$session = $this->sessionCache->get($this->sessionId);
		if (!$session) {
			$session = array();
		}
		$session[$key] = $value;
		global $session_expires_in;
		$this->sessionCache->set($this->sessionId, $session, false, $session_expires_in);
	}

	public function get($key) {
		global $session_expires_in;
		$session = $this->sessionCache->get($this->sessionId);
		$this->sessionCache->set($this->sessionId, $session, false, $session_expires_in);
		if (isset($session[$key])) {
			return $session[$key];
		} else {
			return null;
		}
	}

	public function hasUserId() {
		global $session_expires_in;
		$session = $this->sessionCache->get($this->sessionId);
		$this->sessionCache->set($this->sessionId, $session, $session_expires_in);
		return isset($session[self::$AUTHINDEX]);
	}

	public function getUserId() {
		global $session_expires_in;
		$session = $this->sessionCache->get($this->sessionId);
		$this->sessionCache->set($this->sessionId, $session, $session_expires_in);
		return $session[self::$AUTHINDEX];
	}
}
?>