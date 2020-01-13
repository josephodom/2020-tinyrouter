<?php

define('TINYROUTER_ERROR_SILENT', 0);
define('TINYROUTER_ERROR_EXCEPTION', 1);
define('TINYROUTER_ERROR_EXIT', 2);

class TinyRouter
{
	private $errorHandling;
	
	private $routes = [
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
				if(empty($this->routes[$type]))
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
	
	public function any($routes, $method)
	{
		//
	}
	
	public function get($routes, $method)
	{
		//
	}
	
	public function post($routes, $method)
	{
		//
	}
	
	public function run($routeStr)
	{
		//
	}
}

?>