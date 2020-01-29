<?php

require_once 'functions.php';
require_once 'class.tinyrouter.php';

// First, I'm going to set up how I intend to use it:
$router = new TinyRouter();

// Next, I'll set up some routes:

// Index route
$router->any(':/', function(){
	return 'Index';
});


// CRUD route example:

// Post
$router->get(':/post/new', function(){
	return 'This is the page for creating a new post.<form method="post"><button type="submit">Submit</button></form>';
});
$router->post(':/post/new', function(){
	return 'You have created a new post!';
});

// Routes with & without regex

// Without regex
$router->any(':/post/?/noregex', function($id){
	return 'You are attempting to view post # ' . $id . ' via the no-regex route';
});
// With regex
// This allows more control
// Also, this route manually differentiates between GET and POST
// You could do this, but since the route could be run manually, $_SERVER[REQUEST_METHOD] might not know from which request method the route was called
$router->any('/^\/post\/([0-9]+)\/regex/', function($id){
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		return 'Posting to post # ' . $id . 'via regex route';
	}
	
	return 'You are attempting to view post # '. $id . ' via the regex route'
		. '<br><form method="post"><button type="submit">Go to $_POST?</button></form>';
});

// Multiple routes, one method
$router->any([
	':/multiple/routes',
	':/many/routes',
], function(){
	return 'This method has multiple route strings!';
});

// Display our page
// The run() method gets the route & the request method automatically
// You could manually call routes with the runRoute() method
// e.g. for the route /your/route via GET, use $router->runRoute(':/your/route', 'GET')
//echo $router->run();
include 'template.php';

// Notably missing:
// - PUT support

?>