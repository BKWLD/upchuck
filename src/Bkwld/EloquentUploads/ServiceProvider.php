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
			return new MountManager([
				'local' => new Filesystem(
					new LocalAdapter(public_path().'/uploads/flysystem')
				)
			]);
		});

		// Instantiate observer
		$this->app->bind('eloquent_uploads.observer', function($app) {
			return new Observer($app['request']);
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