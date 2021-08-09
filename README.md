# TinyRouter

A tiny one-file PHP router.

This was built mostly for learning purposes.

## Get Started Immediately

```
// Include the dependency

require_once 'class.tinyrouter.php';

// Initialize your router

$router = new TinyRouter();

// Declare some routes

$router->get(':/', function(){
	// your index page
    
	return 'Index';
});
$router->post(':/subscribe', function(){
	subscribe();
    
	return 'You have subscribed!';
});
$router->any(':/user/?/edit', function($id){
	editUser($id, $_POST);
	
	return 'You are editing the user with the ID # ' . $id;
});

// Run your page

$requestedRoute = $_GET['uri'];
$requestMethod = 'GET';
$router->runRoute($requestedRoute, $requestMethod);
```
