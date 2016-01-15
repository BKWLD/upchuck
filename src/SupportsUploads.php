<?php namespace Bkwld\Upchuck;

/**
 * Adds helper methods for working with the `$upload_attributes` property that
 * should be defined on the model.
 */
trait SupportsUploads {

	/**
	 * The list of uploadable attributes.
	 *
	 * protected $upload_attributes = [ 'image', 'pdf', ];
	 *
	 * @var array
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
