<?php namespace Bkwld\Upchuck;

// Deps
use GrahamCampbell\Flysystem\FlysystemManager as GrahamCampbellFlysystemManager;
use InvalidArgumentException;

/**
 * Despite sharing a similar name to Flysystem's Mount Manager, this class
 * exists to subclass GrahamCampbell/Flysystem so I can redirect where
 * Flysystem configuration is loaded from.
 */
class Manager extends GrahamCampbellFlysystemManager {

	/**
	 * Get the configuration name.
	 *
	 * @return string
	 */
	protected function getConfigName() {
		return 'upchuck';
	}

	/**
	 * Get the configuration for a connection.
	 *
	 * @param string $name Not used but part of parent
	 * @throws InvalidArgumentException
	 * @return array
	 */
	public function getConnectionConfig(string $name = null) {

		// Lookup the connection config
		$config = $this->config->get($this->getConfigName().'.disk');

		// Add cache info the config
		if ($this->config->get($this->getConfigName().'.cache')) {
			$config['cache'] = $this->getCacheConfig();
		}

		// Use the driver as the name.
		$config['name'] = $config['driver'];

		// Return adapter config in the format GrahamCampbell/Flysystem expects
		return $config;
	}

	/**
	 * Get the cache configuration.  Upchuck only uses Illuminate caching.
	 *
	 * @param string $name Not used but part of parent
	 * @throws InvalidArgumentException
	 * @return array
	 */
	protected function getCacheConfig(string $name = null) {
		return [
			'name'      => 'illuminate',
			'driver'    => 'illuminate',
			'key'       => 'upchuck',
		];
	}

}
