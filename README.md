jsonMyCache
===========

Want to pull data into your website/mobile app from an API and you don't want to request the same data over and over from that API?  This PHP file will cache your json, html, or other text results in a MySQL database.  It's just the thing when you don't have memcache on your host.

This works with JSON from an API request or the HTML from your processed templates.  This is a single file you can drop in place on your server.  You'll create a cache with two lines of code and get and set things with two more lines!

Caching JSON, Basecamp API example:

	// pull in jsonMyCache from the current directory
	require_once('jsonMyCache/jsonMyCache.inc.php');

	// create a jsonMyCache caching object
	$config->joc = new jsonMyCache("localhost","username","password","database",'a_table_prefix_namespace'); 

	// Fetch this Basecamp object
	$response = $config->joc->get("/projects.json"); // Check the jsonMyCache namespace for this stored JSON
	if($response == false)
	{
    	$response = bcx_query($endPoint); // It wasn't in our cache, go get a fresh copy from the API
    	$config->joc->set($endPoint,$response); // Cache it.  Now, the next API call will come from the cache!
	}
	$bcxo = json_decode($response); // For convenience, convert that JSON response into PHP data


In the above example I implement a cached wrapper around some Basecamp API calls I wrappesd up inside the bcx_query($endPoint) function.

Caching HTML, processed template example:

	// grab a Twig template we want to render
	$template = $config->twig->loadTemplate('project.html');

	// See if we have rendered HTML for this template in the cache
	$o_html = $config->joc->get('project_html');
	if($o_html == false)    {
      	// No, we don't have the output of this template cached
      	// Render the template
      	$o_html = $template->render(array('navigation' => $config->navigation,
                                'bcx_account' => $bcx_account,
                                'page' => 'Project',
                                'project' => $project,)
                                );
      	// Cache the results of rendering so the next page load will come from the cache
      	$config->joc->set('project_html',$o_html);
	}
	echo  $o_html;

