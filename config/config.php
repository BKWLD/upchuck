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
        'path'       => public_path('uploads'),
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
     * A string that is prepended to the path of the upload (relative to its disk)
     * to convert it from a path to URL resolveable in HTML.
     */
    'url_prefix' => '/uploads/',
    // 'url_prefix' => 'https://your-bucket.s3.amazonaws.com/uploads/',

    /**
     * Whether to delete files when a model is soft deleted.
     */
    'keep_files_when_soft_deleted' => false,

    /**
     * How deep to nest files within subdirectories
     */
    'depth' => 2,

    /**
     * How many folders will be created in each depth
     */
    'length' => 16,
];
