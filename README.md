jsonMyCache
===========

Do you want to pull data into your website/mobile app from an API but you don't want to request the same data over and over from that API?  I wrote the simple json caching code for use on my namecheap shared hosting account which doesn't have access to memcahe.  I do have access to MySQL.  This class library makes it easy to use a MySQL database as a cache for any textual data like the JSON from an API request or the HTML from your processed templates.  This is a single file you can drop in place on your server and begin caching after supplying your database settings with only a couple lines of code.
