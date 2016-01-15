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
	 * Get the path on the disk given the URL.  
	 *
	 * @param string $url 
	 * @return string 
	 */
	public function path($url) {
		$prefix = $this->config['url_prefix'];

		// If the url_prefix is absolute-path style but the url isn't, get only the
		// path from the URL before comparing against the prefix.
		if (preg_match('#^/#', $prefix) && preg_match('#^http#', $url)) {
			$url = parse_url($url, PHP_URL_PATH);
		}

		// Trim the prefix from the URL
		return substr($url, strlen($prefix));
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
