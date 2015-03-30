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
	 * @param string $name
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	public function getConnectionConfig($name) {

		// Lookup the connection
		$config = $this->config->get($this->getConfigName().'::dst');

		// Add cache info the config
		// if (is_string($cache = array_get($config, 'cache'))) {
		// 	$config['cache'] = $this->getCacheConfig($cache);
		// }

		$config['name'] = $config['driver'];

		return $config;
	}

	/**
	 * Get the cache configuration.
	 *
	 * @param string $name
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	protected function getCacheConfig($name) {
		$cache = $this->config->get($this->getConfigName().'::cache');

		if (!is_array($config = array_get($cache, $name)) && !$config) {
				throw new InvalidArgumentException("Cache [$name] not configured.");
		}

		$config['name'] = $name;

		return $config;
	}

}