<?php
date_default_timezone_set('America/Los_Angeles');
include 'config/config.inc';
$services = array();
blockIp();
include 'config/mapping.php';


$_URI = rtrim($_SERVER['REQUEST_URI'], '/');

$_DSESSION = DSession::instance();

date_default_timezone_set('America/Vancouver');
header('X-Powered-By: Confone Inc.');

$_PARAM = array();

global $access_on;
if ($access_on!=0) { Logger::access($_URI); }

$uri = $_URI;
// if $uri is set add .php ot its end for include as file name
//
if (!empty($uri)) {
    $gets = explode('?', $uri, 2);

    if (count($gets)>1) {
	    $getParams = explode('&', $gets[1]);
	    foreach ($getParams as $getParam) {
	        $pair = explode('=', $getParam, 2);
	        if (sizeof($pair)==2) {
	            $_GET[$pair[0]] = urlencode($pair[1]);
	        }
	    }

	    $uri = $gets[0];
    }
}

if (empty($uri)) { $uri = '/index'; }

$uris = explode('/', $uri);
foreach ($services as $key=>$val) {
    $keys = explode('/', $key);

    if (sizeof($uris)==sizeof($keys)) {
        $match = TRUE;
        foreach ($uris as $ind=>$elem) {
            if ($uris[$ind]!=$keys[$ind]) {
                if (strpos($keys[$ind],':') !== false) {
                    $index = substr($keys[$ind], 1);
                    $_PARAM[$index] = $uris[$ind];
                } else {
                    $match = FALSE;
                }
            }
        }

        if ($match) {
            $controller = $services[$key];
            try {
                $controller->execute();
            } catch (Exception $e) {
                Logger::error($e->getMessage());
            	exit;
            }
			exit;
        }
    }
}

// cannot find handler for the request uri, return 404
//
header('HTTP/1.0 404 Not Found');
include 'view/include/404.php';

// ========================================================================

function register($path, $handler) {
    global $services;
    $services[$path] = $handler;
}

function __autoload($class_name) {
    global $autoload_dirs;

    // loop through all configured included folders for the {$class_name}.php file.
    //
    foreach ($autoload_dirs as $dir) {
        if (is_file($dir.'/'.$class_name.'.php')) {
            include ($dir.'/'.$class_name.'.php');
            return;
        }
    }
}

function param($key) {
	if (isset($_POST[$key])) {
		return $_POST[$key];
	} else if (isset($_GET[$key])) {
		return urldecode($_GET[$key]);
	} else {
		global $_PARAM;
		if (isset($_PARAM[$key])) {
			return $_PARAM[$key];
		} else {
			return '';
		}
	}
}

function blockIp() {
    global $ip_block_list;

    $ip = Utility::getClientIp();

    if (isset($ip_block_list[$ip]) && $ip_block_list[$ip]==1) {
        header('HTTP/1.0 403 Forbidden');
        echo '{"error":"403 Forbidden"}';
        exit;
    }
}
?>