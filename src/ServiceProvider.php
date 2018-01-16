<?php namespace Bkwld\Upchuck;

// Deps
use GrahamCampbell\Flysystem\Adapters\ConnectionFactory as AdapterFactory;
use GrahamCampbell\Flysystem\Cache\ConnectionFactory as CacheFactory;
use GrahamCampbell\Flysystem\FlysystemFactory;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;

class ServiceProvider extends LaravelServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {

        // Registers the config file for publishing to app directory
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('upchuck.php')
        ], 'upchuck');

        // Listen for Eloquent saving and deleting
        $this->app['events']->listen('eloquent.saving:*', 'upchuck.observer@onSaving');
        $this->app['events']->listen('eloquent.deleted:*', 'upchuck.observer@onDeleted');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        // Merges package config with user config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'upchuck');

        // Instantiate helpers
        $this->app->singleton('upchuck', function($app) {
            return new Helpers($app['config']->get('upchuck'));
        });

        // Instantiate the disk for the tmp directory, where the image was uploaded
        $this->app->singleton('upchuck.tmp', function($app) {
            $tmp = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
            return new Filesystem(new LocalAdapter($tmp));
        });

        // Instantiate the disk for the destination
        $this->app->singleton('upchuck.disk', function($app) {

            // Build GrahamCampbell\Flysystem's factory for making Flysystem instances
            $adapter = new AdapterFactory();
            $cache = new CacheFactory($app['cache']);
            $factory = new FlysystemFactory($adapter, $cache);

            // Make an instance of this package's subclass of GrahamCampbell\Flysystem's
            // Manager class that creates connections given configs.
            $manager = new Manager($app['config'], $factory);

            // Massage the Upchuck config to what GrahamCampbell\Flysystem is expecting
            return $factory->make($manager->getConnectionConfig(), $manager);
        });

        // Instantiate Flysystem's manager for this package
        $this->app->singleton('upchuck.manager', function($app) {
            return new MountManager([
                'tmp' => $app['upchuck.tmp'],
                'disk' => $app['upchuck.disk'],
            ]);
        });

        // Instantiate observer which handles model save / delete and delegates
        // out the saving of files
        $this->app->singleton('upchuck.observer', function($app) {
            $config = $app['config']->get('upchuck');
            return new Observer($app['upchuck.storage'], $config);
        });

        // Instantiate storage class
        $this->app->singleton('upchuck.storage', function($app) {
            return new Storage(
                $app['upchuck.manager'],
                $app['upchuck'],
                $app['config']->get('upchuck.depth'),
                $app['config']->get('upchuck.length')
            );
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array(
            'upchuck',
            'upchuck.disk',
            'upchuck.tmp',
            'upchuck.manager',
            'upchuck.observer',
            'upchuck.storage',
        );
    }

}
