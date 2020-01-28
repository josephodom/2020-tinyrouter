<?php

define('TINYROUTER_ERROR_SILENT', 0);
define('TINYROUTER_ERROR_EXCEPTION', 1);
define('TINYROUTER_ERROR_EXIT', 2);

class TinyRouter
{
	private $errorHandling;
	
	public $routes = [
		'get' => [],
		'post' => [],
	];
	
	function __construct($errorHandling = TINYROUTER_ERROR_SILENT)
	{
		$this->errorHandling = $errorHandling;
	}
	
	private function maybeStrToArray($routes)
	{
		if(is_array($routes))
		{
			return $routes;
		}
		
		return [ $routes, ];
	}
	
	private function add($routes, $method, $types)
	{
		$routes = $this->maybeStrToArray($routes);
		$types = $this->maybeStrToArray($types);
		
		foreach($routes as $route)
		{
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
	
	public function has($needle, $types = [ 'get', 'post', ])
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
		$this->add($routes, $method, [ 'get', 'post', ]);
	}
	
	public function get($routes, $method)
	{
		$this->add($routes, $method, 'get');
	}
	
	public function post($routes, $method)
	{
		$this->add($routes, $method, 'post');
	}
	
	public function run($routeStr, $type)
	{
		if(!isset($this->routes[$type]))
		{
			$this->error('Route type `' . $type . '` is not valid');
			
			return false;
		}
		
		if(!isset($this->routes[$type][$routeStr]))
		{
			$this->error('Route  `' . $type . '@' . $routeStr . '` does not exist');
			
			return false;
		}
		
		return $this->routes[$type][$routeStr]();
	}
}

?>