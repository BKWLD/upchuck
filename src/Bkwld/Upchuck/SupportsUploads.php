<?php namespace Bkwld\Upchuck;

/**
 * Adds helper methods for working with the `$upload_attributes` property that
 * should be defined on the model.
 */
trait SupportsUploads {

	/**
	 * The list of uploadable attributes.  If a key-val pair, the key is the
	 * input attribute and the value is the model attribtue. Example:
	 *
	 *     private $upload_attributes = [
	 *       'image',
	 *       'bkgd' => 'image',
	 *     ];
	 * 
	 * @var array
	 */
	 private $upload_attributes = [];

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
	 * @return array Keys are input keys, values are model attributes
	 */
	public function getUploadMap() {
		$map = [];
		foreach($this->getUploadAttributes() as $key => $val) {
			$map[is_numeric($key)?$val:$key] = $val;
		}
		return $map;
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
