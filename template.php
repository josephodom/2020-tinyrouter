<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="/resume/tinyrouter/">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="./css/style.css">
<title>TinyRouter &bull; A tiny PHP router</title>
</head>
<body>

<main id="main">
	<section id="hero">
		<div class="container">
			<h1>TinyRouter</h1>
			
			<h2>A tiny PHP router</h2>
		</div><!-- .container -->
	</section>
	
	<section class="py-5">
		<div class="container">
			<h2>Getting Started</h2>
			
			<p>
				TinyRouter is simple to use. Just <strong>initialize</strong>, <strong>set routes</strong>, and <strong>run</strong>!
			</p>
			
			<p>
				<?=codeFile('example.txt')?>
			</p>
		</div><!-- .container -->
	</section>
	
	<section class="py-5">
		<div class="container">
			<h2>Autorun</h2>
			
			<p>
				TinyRouter can get the route string & request method for you. Here&rsquo;s how:
			</p>
			
			<p>
				<?=codeFile('run.txt')?>
			</p>
			
			<p>
				You&rsquo;ll need to do a little bit of work to get it to read your requests, too. Here&rsquo;s an htaccess example:
			</p>
			
			<p>
				<?=codeFile('htaccess.txt')?>
			</p>
			
			<p>
				This redirects all requests to index.php, and the route string gets turned into a GET parameter called &ldquo;uri&rdquo;,
				which TinyRouter uses to get the string.
			</p>
		</div><!-- .container -->
	</section>
	
	<section class="py-5">
		<div class="container">
			<h2>404 Page</h2>
			
			<p>
				To create a 404 page, simply check if running the router returns FALSE:
			</p>
			
			<p>
				<?=codeFile('404.txt')?>
			</p>
		</div><!-- .container -->
	</section>
	
	<section class="py-5">
		<div class="container">
			<h2>Precise Routes</h2>
			
			<p>
				If you need something a little more precise, you can use regex to match your route strings.
			</p>
			
			<p>
				<?=codeFile('regex.txt')?>
			</p>
		</div><!-- .container -->
	</section>
</main>

</body>
</html>