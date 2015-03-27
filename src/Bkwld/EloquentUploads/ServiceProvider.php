<?php namespace Bkwld\EloquentUploads;

// Deps
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

		// Listen for Eloquent saving and deleting
		$this->app['events']->listen('eloquent.saving:*', 'eloquent_uploads.observer@onSaving');
		$this->app['events']->listen('eloquent.deleting:*', 'eloquent_uploads.observer@onDeleting');

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {

		// Instantiate Flysystem for this package
		$this->app->bind('eloquent_uploads.flysystem_manager', function($app) {

			// Get the temp directory, this is where uploads will be moved from
			$tmp = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();

			// Get the dst directory from the config
			$dst = public_path().'/uploads';

			// Create the MountManger instance
			return new MountManager([
				'tmp' => new Filesystem(new LocalAdapter($tmp)),
				'dst' => new Filesystem(new LocalAdapter($dst)),
			]);
		});

		// Instantiate observer
		$this->app->bind('eloquent_uploads.observer', function($app) {
			return new Observer($app['request'], $app['eloquent_uploads.storage']);
		});

		// Instantiate storage class
		$this->app->bind('eloquent_uploads.storage', function($app) {
			return new Storage($app['eloquent_uploads.flysystem_manager']);
		});

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array(
			'eloquent_uploads.flysystem_manager',
			'eloquent_uploads.observer',
			'eloquent_uploads.storage',
		);
	}

}