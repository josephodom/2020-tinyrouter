<?php

define('TINYROUTER_ERROR_SILENT', 0);
define('TINYROUTER_ERROR_EXCEPTION', 1);
define('TINYROUTER_ERROR_EXIT', 2);

class TinyRouter
{
	private $errorHandling;
	
	public $routes = [
		'GET' => [],
		'POST' => [],
	];
	
	function __construct($errorHandling = TINYROUTER_ERROR_SILENT)
	{
		$this->errorHandling = $errorHandling;
	}
	
	private static function isRegex($str)
	{
		if(substr($str, 0, 1) != '/')
		{
			return false;
		}
		
		if(substr($str, -1, 1) != '/')
		{
			return false;
		}
		
		return true;
	}
	
	private static function maybeStrToArray($routes)
	{
		if(is_array($routes))
		{
			return $routes;
		}
		
		return [ $routes, ];
	}
	
	private static function stringToRegex($str)
	{
		// If there is not : at the beginning, trust that it's already regex
		if(substr($str, 0, 1) != ':')
		{
			return $str;
		}
		
		$str = substr($str, 1);
		$str = str_replace('/', '\/', $str);
		$str = str_replace('?', '([^\/]+)', $str);
		$str = '/^' . $str . '$/';
		
		return $str;
	}
	
	private function add($routes, $method, $types)
	{
		$routes = $this->maybeStrToArray($routes);
		$types = $this->maybeStrToArray($types);
		
		foreach($routes as $route)
		{
			$route = $this->stringToRegex($route);
			
			foreach($types as $type)
			{
				if(!isset($this->routes[$type]))
				{
					$this->error('Route type `' . $type . '` is not valid');
					
					continue;
				}
				
				$this->routes[$type][$route] = $method;
			}
		}
	}
	
	private function error($errorString)
	{
		switch($this->errorHandling)
		{
			// Do nothing
			case TINYROUTER_ERROR_SILENT:
			{
				return;
			}
			
			case TINYROUTER_ERROR_EXCEPTION:
			{
				throw new Exception($errorString);
			}
			
			case TINYROUTER_ERROR_EXIT:
			{
				die($errorString);
			}
		}
	}
	
	public function has($needle, $types = [ 'GET', 'POST', ])
	{
		$types = $this->maybeStrToArray($types);
		
		foreach($types as $type)
		{
			foreach(array_keys($this->routes[$type]) as $route)
			{
				if($route == $needle)
				{
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function any($routes, $method)
	{
		$this->add($routes, $method, [ 'GET', 'POST', ]);
	}
	
	public function GET($routes, $method)
	{
		$this->add($routes, $method, 'GET');
	}
	
	public function POST($routes, $method)
	{
		$this->add($routes, $method, 'POST');
	}
	
	public function runRoute($routeStr, $type)
	{
		if(!isset($this->routes[$type]))
		{
			$this->error('Route type `' . $type . '` is not valid');
			
			return false;
		}
		
		foreach(array_keys($this->routes[$type]) as $route)
		{
			if(preg_match_all($route, $routeStr, $matches))
			{
				$func = $this->routes[$type][$route];
				
				$params = [];
				
				if(!empty($matches[1]))
				{
					$params = $matches[1];
				}
				
				return call_user_func_array($func, $params);
			}
		}
		
		$this->error('Route  `' . $type . '@' . $routeStr . '` does not exist');
		
		return false;
	}
	
	public function run()
	{
		$uri = '/';
		
		if(!empty($_GET['uri']))
		{
			$uri .= $_GET['uri'];
		}
		
		return $this->runRoute($uri, $_SERVER['REQUEST_METHOD']);
	}
}

?>