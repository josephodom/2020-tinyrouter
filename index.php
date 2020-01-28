<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'class.tinyrouter.php';

// First, I'm going to set up how I intend to use it:

$router = new TinyRouter();

// Without regex
$router->any('/post/?/noregex', function($id){
	return 'You are attempting to view post # ' . $id . ' via the no-regex route';
});

// With regex
// This allows more control
$router->any('/^\/post\/([0-9]+)\/regex/', function($id){
	return 'You are attempting to view post # '. $id . ' via the regex route';
});

// Post
$router->get('/post/new', function(){
	return 'This is the page for creating a new post.<form method="post"><button type="submit">Submit</button></form>';
});
$router->post('/post/new', function(){
	return 'You have created a new post!';
});

// Multiple routes, one method
$router->any([
	'/multiple/routes',
	'/many/routes',
], function(){
	return 'This method has multiple route strings!';
});

// Display our page
echo $router->run('/multiple/routes', 'get');

// Notably missing:
// - PUT support

?>