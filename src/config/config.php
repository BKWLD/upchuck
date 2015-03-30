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
		// 'cache'      => 'foo',

		/**
		 * AWS S3 example
		 */
		// 'driver'     => 'awss3',
		// 'key'        => 'your-key',
		// 'secret'     => 'your-secret',
		// 'bucket'     => 'your-bucket',
		// 'region'     => 'your-region',
		// 'visibility' => 'public',

		/**
		 */

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
	 * Parse the URL from the absolute path to file using this
	 * regex.  Make sure the absolute path can be found in the $1
	 * regex reference.
	 */
	'parse_url' => public_path().'(.*)',

];