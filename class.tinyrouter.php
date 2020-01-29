<?php

define('TINYROUTER_ERROR_SILENT', 0);
define('TINYROUTER_ERROR_EXCEPTION', 1);
define('TINYROUTER_ERROR_EXIT', 2);

class TinyRouter
{
	/**
	 * Setting for how to handle errors. Use one of the TINYROUTER_ERROR constants
	 *
	 * @var int
	 */
	private $errorHandling;
	
	/**
	 * Associative array of accepted request types, containing associative arrays of routes => methods. E.g. $routes['GET']['/\/index\/'] = function(){}
	 *
	 * @var array
	 */
	private $routes = [
		'GET' => [],
		'POST' => [],
	];
	
	/**
	 * Contrustor. Allows you to set $errorHandling
	 *
	 * @param int $errorHandling
	 */
	function __construct($errorHandling = TINYROUTER_ERROR_SILENT)
	{
		$this->errorHandling = $errorHandling;
	}
	
	/**
	 * Adds a route. A private function that provides the backbone for the public functions `any`, `get`, and `post`
	 *
	 * @param mixed $routes Can be a string, or an array of strings
	 * @param callable $method Function to execute when this route is visited. Should return a string
	 * @param mixed $types Can be a string, or an array of strings
	 * @return void
	 */
	private function add($routes, callable $method, $types) : void
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
	
	/**
	 * Reports an error based on the $errorHandling setting
	 *
	 * @param string $errorString
	 * @return void
	 */
	private function error(string $errorString) : void
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
	
	/**
	 * See if a route has been registered
	 * 
	 * @param string $needle The route you're looking for
	 * @param mixed An array of request methods, or a string with your request method
	 * @return bool
	 */
	public function has(string $needle, $types = [ 'GET', 'POST', ]) : bool
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
	
	/**
	 * Add a route to GET & POST
	 *
	 * @param mixed $routes Can be a string, or an array of strings
	 * @param callable $method Function to execute when this route is visited. Should return a string
	 * @return void
	 */
	public function any($routes, callable $method)
	{
		$this->add($routes, $method, [ 'GET', 'POST', ]);
	}
	
	/**
	 * Add a route to GET
	 *
	 * @param mixed $routes Can be a string, or an array of strings
	 * @param callable $method Function to execute when this route is visited. Should return a string
	 * @return void
	 */
	public function GET($routes, callable $method) : void
	{
		$this->add($routes, $method, 'GET');
	}
	
	/**
	 * Add a route to POST
	 *
	 * @param mixed $routes Can be a string, or an array of strings
	 * @param callable $method Function to execute when this route is visited. Should return a string
	 * @return void
	 */
	public function POST($routes, callable $method) : void
	{
		$this->add($routes, $method, 'POST');
	}
	
	/**
	 * Runs the route method associated with your route string & request method, if it exists
	 *
	 * @param string $routeStr
	 * @param string $type Request method, e.g. GET or POST
	 * @return mixed string type on success, FALSE on failure
	 */
	public function runRoute(string $routeStr, string $type)
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
	
	/**
	 * Basic run function that automatically grabs the URI $_GET param & the REQUEST_METHOD var from $_SERVER
	 *
	 * @return mixed string type on success, FALSE on failure
	 */
	public function run()
	{
		$uri = '/';
		
		if(!empty($_GET['uri']))
		{
			$uri .= $_GET['uri'];
		}
		
		return $this->runRoute($uri, $_SERVER['REQUEST_METHOD']);
	}
	
	/**
	 * Static function for determining whether a string is regex or not. Imperfect method! Tailored to this class's priorities.
	 *
	 * @param string $str
	 * @return boolean
	 */
	private static function isRegex($str) : bool
	{
		if(substr($str, 0, 1) == ':')
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns either an array where index 0 is your parameter or, if the param is an array, it returns it unchanged.
	 *
	 * @param mixed $routes
	 * @return array
	 */
	private static function maybeStrToArray($routes) : array
	{
		if(is_array($routes))
		{
			return $routes;
		}
		
		return [ $routes, ];
	}
	
	/**
	 * Turns your string into regex. Imperfect method! Tailored to this class's priorities.
	 *
	 * @param string $str
	 * @return string
	 */
	private static function stringToRegex(string $str) : string
	{
		// If there is not : at the beginning, trust that it's already regex
		if(substr($str, 0, 1) != ':')
		{
			return $str;
		}
		
		// Get rid of the :
		$str = substr($str, 1);
		// Escape slashes
		$str = str_replace('/', '\/', $str);
		// Replace ? with a variable that encompasses every character until a /
		// This is very basic, but if you need something more precise, use regex
		$str = str_replace('?', '([^\/]+)', $str);
		// Add beginning & end slashes
		// Also add symbols to make sure the route encompasses the entire route string
		// If you need a partial route string, use regex
		$str = '/^' . $str . '$/';
		
		return $str;
	}
}

?>