<?php namespace Bkwld\Upchuck;

/**
 * Helpers generally invoked from the Faade
 */
class Helpers {

	/**
	 * @var array The Upchuck config array
	 */
	private $config;

	/**
	 * Inject dependencies
	 *
	 * @param array $config 
	 */
	public function __construct($config) {
		$this->config = $config;
	}

	/**
	 * Check whether Upchuck manages the given URL
	 *
	 * @param string $url 
	 * @return boolean 
	 */
	public function manages($url) {
		return preg_match('#^'.$this->config['url_prefix'].'#', $url) > 0;
	}

	/**
	 * Get the path form a URL to the disk
	 *
	 * @param string $url 
	 * @return string 
	 */
	public function path($url) {
		return substr($url, strlen($this->config['url_prefix']));
	}

	/**
	 * Get a URL of an upload given the path to an asset
	 *
	 * @param string $path 
	 * @return string
	 */
	public function url($path) {
		return $this->config['url_prefix'].ltrim($path, '/');
	}

}