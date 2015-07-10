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
	 * Get the path on the disk given the URL.  If the url_prefix is absolute-path
	 * style, get the path from the URL before subtracting the prefix.
	 *
	 * @param string $url A full URL (https://....)
	 * @return string 
	 */
	public function path($url) {
		$prefix = $this->config['url_prefix'];
		if (preg_match('#^/#', $prefix)) $url = parse_url($url, PHP_URL_PATH);
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
