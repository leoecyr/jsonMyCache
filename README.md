jsonMyCache
===========

Want to pull data into your website/mobile app from an API and you don't want to request the same data over and over from that API?  This PHP file will cache your json, html, or other text results in a MySQL database.  It's just the thing when you don't have memcache on your host.

This works with JSON from an API request or the HTML from your processed templates.  This is a single file you can drop in place on your server.  You'll create a cache with two lines of code and get and set things with two more lines!

Caching JSON, Basecamp API example:

	// pull in jsonMyCache  and create a jsonMyCache cache object
	require_once('jsonMyCache/jsonMyCache.inc.php');
	$config->joc = new jsonMyCache("localhost","username","password","database",'a_table_prefix_namespace'); 

	// Fetch Basecamp projects
	$response = $config->joc->get("/projects.json");
	if($response == false)
	{
		// It wasn't in the cache.  Get it and cache it.
    		$response = bcx_query($endPoint);
    		$config->joc->set($endPoint,$response);
	}

	// Do something with that response!
	$bcxo = json_decode($response);


Caching HTML, processed template example:

	// pull in jsonMyCache  and create a jsonMyCache cache object
	require_once('jsonMyCache/jsonMyCache.inc.php');
	$config->joc = new jsonMyCache("localhost","username","password","database",'a_table_prefix_namespace'); 

	// grab a Twig template we want to render
	$template = $config->twig->loadTemplate('project.html');

	// Check the cache for this template
	$o_html = $config->joc->get('project_html');
	if($o_html == false)    {
		//  It's not in the cache.  Render it and cache it.
      		$o_html = $template->render(array('project' => $project));
      		$config->joc->set('project_html',$o_html);
	}

	// Deliver the page
	echo  $o_html;

