<? namespace Bkwld\Upchuck;

/**
 * Adds helper methods for working with the `$upload_attributes` property that
 * should be defined on the model.
 */
trait SupportsUploads {

	/**
	 * Define a `private $upload_attributes` property like.  It supports syntaxes
	 * like:
	 *
	 * 		private $upload_attributes = [
	 * 			'image',
	 * 			'bkgd' => 'image',
	 * 		];
	 */

	/**
	 * Return the raw upload_attributes configuration
	 *
	 * @return array 
	 */
	public function getUploadAttributes() {
		if (!isset($this->upload_attributes)) return [];
		return $this->upload_attributes;
	}

	/**
	 * Massage the attribute configuration so that all keys represent input fields 
	 * and all values represent model attributes.  If a node doesn't have key and
	 * val, the val is used for both.
	 *
	 * @return array  
	 */
	public function getUploadConfig() {
		$config = [];
		foreach($this->getUploadAttributes() as $key => $val) {
			$config[is_numeric($key)?$val:$key] = $val;
		}
		return $config;
	}

	/**
	 * Set a model attribute for an uploaded file to the URL of that file. Uses
	 * `fill()` so that mass assignment prevention will apply.
	 *
	 * @param string $attribute 
	 * @param string $url 
	 */
	public function setUploadAttribute($attribute, $url) {
		$this->fill([$attribute => $url]);
	}

}