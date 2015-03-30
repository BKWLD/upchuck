<?php namespace Bkwld\Upchuck;

// Deps
use GrahamCampbell\Flysystem\Adapters\ConnectionFactory as AdapterFactory;
use GrahamCampbell\Flysystem\Cache\ConnectionFactory as CacheFactory;
use GrahamCampbell\Flysystem\Factories\FlysystemFactory;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;

class ServiceProvider extends LaravelServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {
		$this->package('bkwld/upchuck');

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

		// Instantiate the disk for the destination
		$this->app->bind('upchuck.disk', function($app) {

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
		$this->app->bind('upchuck.manager', function($app) {

			// Get the temp directory, this is where uploads will be moved from
			$tmp = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();

			// Create the MountManger instance
			return new MountManager([
				'tmp' => new Filesystem(new LocalAdapter($tmp)),
				'disk' => $app['upchuck.disk'],
			]);
		});

		// Instantiate observer which handles model save / delete and delegates
		// out the saving of files
		$this->app->bind('upchuck.observer', function($app) {
			return new Observer($app['request'], $app['upchuck.storage']);
		});

		// Instantiate storage class
		$this->app->bind('upchuck.storage', function($app) {
			return new Storage($app['upchuck.manager'], $app['config']->get('upchuck::url_prefix'));
		});

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array(
			'upchuck.disk',
			'upchuck.manager',
			'upchuck.observer',
			'upchuck.storage',
		);
	}

}