<?php return [
	
	/**
	 * Flysystem mount config for the destination of your uploads. For other 
	 * examples, check out the source of GrahamCampbell/Laravel-Flysystem config.
	 * That package's factory class s used to instante Flysystem in Upchuck and 
	 * all of it's configs are supported.
	 * 
	 * See: https://github.com/GrahamCampbell/Laravel-Flysystem/blob/1.0/src/config/config.php
	 *
	 * Note, for each driver except "local", you must require the adapter package:
	 *
	 * See: https://github.com/thephpleague/flysystem#adapters
	 */
	'disk' => [

		/**
		 * Local exaample
		 */
		'driver'     => 'local',
		'path'       => public_path().'/uploads',
		'visibility' => 'public',

		/**
		 * AWS S3 example
		 */
		// 'driver'     => 'awss3',
		// 'key'        => 'your-key',
		// 'secret'     => 'your-secret',
		// 'bucket'     => 'your-bucket',
		// 'prefix'     => 'uploads/',
		// 'visibility' => 'public',

	],

	/**
	 * Enable Flysystem caching using Laravel's current cache provider.  You must
	 * require Flysystem's cache adapter package, league/flysystem-cached-adapter,
	 * if enabled.  You should enable this if you are using a non-local disk.
	 * 
	 * See: http://flysystem.thephpleague.com/caching/
	 */
	'cache' => false,

	/**
	 * A closure that takes the path of the uploaded image (relative to the config
	 * of your disk) and converts it to a URL that could be rendered in HTML.
	 * 
	 * @param string $path A relative path to the file on the disk
	 * @return string A URL to the resource that could be rendered in HTML
	 */
	'url_generator' => function($path) {
		switch(Config::get('upchuck::disk.driver')) {

			// Make local paths relative to the document root.  Not including the hostname
			// so migrating between enviornments is simpler
			case 'local': return '/uploads/'.$path;

			// Make a URL using the bucket name
			case 'awss3': return 'https://'.Config::get('upchuck::disk.bucket').'.s3.amazonaws.com/uploads/'.$path;

			// Return the path if the driver doesn't have converter yet
			default: return $path;
		}
	},

];